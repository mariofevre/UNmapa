<?php
/**
*MAPAgenerico.php
*
* aplicación para generar mapas de visualizacion de datos genericos (no pertenecientes a una actividad)
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	mapas
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}


$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';

$MODO = $_GET['modo'];


?>
<head>
	<title>Panel de control</title>
	<?php 
	include("./includes/meta.php");
	?>
	 <style type='text/css'>
	 	body, input, form{
	 		font-size:9px;
	 		font-family:arial;
	 		margin:0;
	 		
	 	}
	 	input{
	 		width:200px;
	 	}
	 	
	 	input[type='submit']{
	 		width:80px;
	 		display:block;
	 		margin:2px;
	 		margin-top:4px;
	 	}	 	
	 	input[readonly='readonly']{
	 		width:42px;
	 		display:inline;
	 		margin:0px;
	 		background-color:transparent;
	 		border:none;
	 	}
	 	
	 	label{
	 		margin-top:5px;
	 		display: inline-block;
	 		font-size:12px;
	 		font-weight:bold;
	 	}
	 a{
	 	display: inline-block;
	    max-height: 13px;
	    max-width: 110px;
	    overflow: hidden;
	 }
	 p{
	 	margin:0px;
	 }
 	 #divMapa { 
 	 	 
 	 	height: 300px; 
		border: solid 2px #808080; 
		}
		
		a.olControlZoomOut.olButton{
			font-size:10px;
			line-height:10px;
			width:13px;
		}
		a.olControlZoomIn.olButton{
			font-size:10px;
			line-height:10px;
			width:13px;
		}		
		div.olControlZoom.olControlNoSelect {
		    left: 3px;
		    top: 3px;
		}
		.olControlAttribution olControlNoSelect{
		 	bottom: 10px;
		}
	 </style>
	 
	
<?php

function obtenerPuntos($Actividad,$Escala,$Nivel,$Area){
	
//print_r($LocalizacionesAct);	
// consulta puntos de la base colectiva de todas las actividades

	//$Puntos['Activ']=$LocalizacionesAct;
	//$Puntos['Base']=$LocalizacionesBase;	
	return $Puntos;
}

	
	$Puntos = obtenerPuntos($Actividad['id'],$Escala,$Usuario['nivel'],$Usuario['areas']);

	
	?>
   <script language="javascript">
      function configure() {
		var template = document.demo.template.options[document.demo.template.selectedIndex].value;
        var snippet = " TEMPLATE " + template;
        document.demo.map_web.value = snippet;
        if(template.indexOf("frame") != -1) document.demo.action = "frames.html";
        if(template.indexOf("dhtml") != -1) document.demo.action = "frames_dhtml.html";
      }      
    </script>
    
    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>	    
    
    <script type="text/javascript">
        // Definimos las variables globales 'mapa' y 'capa'
        var mapa, capa, marcklayer, vectorLayer, controls, drawPoint, point_style;
        

        // Definimos una función que arranque al cargar la página
        window.onload= function() {
            // Creamos el mapa
            var mapa = new OpenLayers.Map("divMapa");

            // Creamos una capa base
            
			
 	        // Creamos una capa fondo con un servicio wms
             var capa = new OpenLayers.Layer.WMS( 
                "Calles en OWS terrestris",
                "http://ows.terrestris.de/osm/service?styles=&amp;layer=OSM-WMS&amp;service=WMS&amp;srs=epsg:4326&amp;format=image%2Fpng&amp;sld_version=1.1.0&", 
                {layers: 'OSM-WMS'},	                
                {attribution:"Base OSM bajo servidor irs gis lab"}
            );		
   
            
               var capaB = new OpenLayers.Layer.WMS( 
                "Calles en mundialis",	       
	           		"http://ows.mundialis.de/services/service?",
	           		{layers: 'OSM-WMS'},
	                {attribution:"Base OSM bajo servidor mundialis"}
	              );
              
            // Creamos una capa fondo con un servicio wms
            var capaD = new OpenLayers.Layer.WMS( 
                "Calles en Geofabrik",	                
                "http://full.wms.geofabrik.de/web/975d3dc24139f06ce8306f9353d28c10?", 
                {layers: 'basic'},
                {attribution:"Base OSM bajo servidor GEOFABRIK"}
            );
            				            		
            var styleMap = new OpenLayers.StyleMap({pointRadius: 2,}			                         );

            		
 			//creamos capa de puntos de la actividad
            baseLayer = new OpenLayers.Layer.Vector("Nuevos Base", {styleMap: styleMap});
	

	 		//creamos capa de puntos de la actividad
            vectorLayer = new OpenLayers.Layer.Vector("Nuevos puntos");
		<?php
			$minLat=180;
			$minLon=180;
			$maxLat=-180;
			$maxLon=-180;
			
			foreach($Puntos['Base'] as $LAid => $LAdata ){
				$minLat=min($minLat,$LAdata['lat']);
				$minLon=min($minLon,$LAdata['lon']);
				$maxLat=max($maxLat,$LAdata['lat']);
				$maxLon=max($maxLon,$LAdata['lon']);				
				echo "
					var point =
				        new OpenLayers.Geometry.Point(".$LAdata['lon'].", ".$LAdata['lat'].");
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes       
					baseLayer.addFeatures(pointFeature);					
				//parent.document.getElementById('recuadro2').innerHTML=parent.document.getElementById('recuadro2').innerHTML+'<li>".$LAdata['lon'].", ".$LAdata['lat']." nuevo punto</li>';
				";		
			}	
		?>
 		//creamos capa de puntos de la actividad
        vectorLayer = new OpenLayers.Layer.Vector("Nuevos puntos");
		<?php
			foreach($Puntos['Activ'] as $LAid => $LAdata ){
				$minLat=min($minLat,$LAdata['lat']);
				$minLon=min($minLon,$LAdata['lon']);
				$maxLat=max($maxLat,$LAdata['lat']);
				$maxLon=max($maxLon,$LAdata['lon']);
								
				echo "
					var point =
				        new OpenLayers.Geometry.Point(".$LAdata['lon'].", ".$LAdata['lat'].");
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes       
					vectorLayer.addFeatures(pointFeature);			
				
				//parent.document.getElementById('recuadro3').innerHTML=parent.document.getElementById('recuadro3').innerHTML+'<li>".$LAdata['lon'].", ".$LAdata['lat']." nuevo punto</li>';
				
				";		
			}	
			$lon=($maxLon+$minLon)/2;
			$lat=($maxLat+$minLat)/2;
			?>


            // Añadimos las capas al mapa
            mapa.addLayers([capa, vectorLayer, baseLayer]);
            // Fijamos centro y zoom
            mapa.zoomToMaxExtent();

            mapa.addControl(new OpenLayers.Control.MousePosition());

			mapa.setCenter(new OpenLayers.LonLat(-58.87533569336, -34.55833358768), 7);
            
            vectorLayer.events.register('beforefeatureadded', vectorLayer, function (limpiar) {
            vectorLayer.removeAllFeatures();
			});   
		
        }

	</script>		
	</head>
	<body>
	
	<div id="divMapa"></div>
	
	<form method='POST' action='./argumentacionlocalizacion.php' enctype='multipart/form-data'>
	<input type='hidden' value='<?php echo $Actividad;?>' name='argumentacion'>
	<input type='hidden' value='<?php echo $_GET['localizacion'];?>' name='localizacion'>
	<input style='display:none;' type='button' value='confirmo borrar' onclick='this.parentNode.submit();'>
	</form>

</body>

