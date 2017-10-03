<?php
/**
*MAPAactividad.php
*
*
* aplicación para generar mapas para el dearrollo de una actividad, permitiendo la visualización y carga de puntos)
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

//include("./includes/BASEconsultas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}

// función de consulta de actividades a la base de datos 
include("./actividades_consulta.php");

$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';

//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");
?>
<head>
	<title>Panel de control</title>
	<?php 
	include("./includes/meta.php");
	?>
	<link href="css/UNmapa.css" rel="stylesheet" type="text/css">	
	 <script src="./js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	 <style type='text/css'>
	 	body, input, form{
	 		font-size:10px;
	 		margin:0;
	 		overflow:hidden;
	 	}
	 	input{
	 		width:200px;
	 	}
	 	
	 	input[type='inputsubmit']{
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
		border: none; 
		position: relative;
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
	#modelos{
		display:none;
	}
	
	div.dataLayersDiv{
		display:none;
	}
	div.baseLayersDiv > input{
		width:12px;
    	vertical-align:middle;
      height: 15px;
	}
	
	div.baseLayersDiv > label {
	    vertical-align:middle;
	    width: 150px;
   }
   
	div.olControlLayerSwitcher div.layersDiv { 
	    padding: 2px;
	}
	
	div.dataLbl{
		display:none;	
	}
	div.baseLayersDiv{
		padding-left: 4px;
	}

	 </style>
	 
	
<?php

include_once('./BASEconsultas.php');

// define si se esta defininedo un punto, de no ser así esta aplicación solo devuelve un mensaje escrito
if(isset($_POST['actividad'])){
	$ActividadId=$_POST['actividad'];
	$ACCION = $_POST['accion'];
	$RID=$_POST['rid'];
	$_GET['rid']=$RID;

}elseif(isset($_GET['actividad'])){
	$ActividadId=$_GET['actividad'];
}else{
	$ActividadId='';
}
if($ActividadId==''){
	//header('location: ./actividades.php');	//si no hay una actividad definida esta página no debería consultarse
	echo "ERROR de Acceso 1";
	break;
}

if($_GET['rid']>0){
	$Registro=$_GET['rid'];
}

$query="
	SELECT `ACTaccesos`.`id`,
	    `ACTaccesos`.`id_actividades`,
	    `ACTaccesos`.`id_usuarios`,
	    `ACTaccesos`.`nivel`
	FROM `UNmapa`.`ACTaccesos`
	WHERE id_actividades='".$ActividadId."'
";
$Consulta = mysql_query($query,$Conec1);
echo mysql_error($Conec1);

while($row=mysql_fetch_assoc($Consulta)){
	$Accesos[$row['id_usuarios']]=$row['nivel'];
}


$Actividad=reset(actividadesconsulta($ActividadId,null));//carga los datos de la aactividad mediante esta funcion en atctividades_consulta.php

$Accesos[$Actividad['zz_AUTOUSUARIOCREAC']]='10';

//echo "<pre>";print_r($Actividad);echo "</pre>";

//print_r($Accesos);
if($Accesos[$UsuarioI]<1&&$Actividad['abierta']!='1'){
	//header('location: ./actividades.php');	//este usuario deber definir una actividad habilitada
	echo "ERROR de Acceso 2 u:".$UsuarioI." Aab:".$Actividad['abierta'];
	break;
}

if (isset($_POST['x']))
	$_POST['x']=str_replace(",",".",$_POST['x']);

if (isset($_POST['y']))
	$_POST['y']=str_replace(",",".",$_POST['y']);

if($ACCION=="crear"&&$UsuarioI>0){
	
	$query="
		INSERT INTO 
			`UNmapa`.`geodatos`
			SET
			`x`='".$_POST['x']."',
			`y`='".$_POST['y']."',
			`geometria`='".$_POST['geometria']."',
			`id_usuarios`='$UsuarioI',
			`id_actividades`='".$Actividad['id']."',
			`fecha`='$HOY',
			`z`='".$_POST['z']."'
			
	";
	
	$Consulta = mysql_query($query,$Conec1);
	$NID=mysql_insert_id($Conec1);
	
	echo "<div class='aux'>";
		echo mysql_error($Conec1);	
		echo "punto creado";
	echo "</div>";


	if($_POST['nuevacategoria']!=''){
		$query="
			INSERT INTO 
				`UNmapa`.`ACTcategorias`
				SET
					`id_p_actividades_id`='".$Actividad['id']."',
					`nombre`='".$_POST['nuevacategoria']."',
					`descripcion`='".$_POST['nuevacategoria']."'
			";			
		$Consulta = mysql_query($query,$Conec1);
		$_POST['categoria']=mysql_insert_id($Conec1);	
		echo "<div class='aux'>";
			echo mysql_error($Conec1);
			echo "categoría creada $ncID";
		echo "</div>";	
	}

	$valor = "null";
	if (isset($_POST['valor']))
		$valor = "'".isset($_POST['valor'])."'";
	$query="
		INSERT INTO 
			`UNmapa`.`atributos`
			SET
				`id`='$NID',
				`valor`=".$valor.",
				`categoria`='".$_POST['categoria']."',
				`texto`='".$_POST['texto']."',
				`link`='".$_POST['link']."',
				`textobreve`='".$_POST['textobreve']."',
				`id_usuarios`='".$UsuarioI."',
				`id_actividades`='".$Actividad['id']."',
				`fecha`='".$HOY."',
				`escala`='".$_POST['escala']."',
				`nivelUsuario`='".$Usuario['nivel']."',
				`areaUsuario`='".$Usuario['area']."'
		";
	error_log($query);
	$Consulta = mysql_query($query,$Conec1);
	echo "<div class='aux'>";
		echo mysql_error($Conec1);
		echo "punto creado $NID";
		echo "
			<script type='text/javascript'>
			parent.window.location='./actividad.php?actividad=".$Actividad['id']."';
			</script>	
		";		
	echo "</div>";
		
}elseif($ACCION=="borrar"&&$UsuarioI>0){
	// si el formulario de reingreso así lo definió modifica el registro como borrado
	$query="
		UPDATE 
			`UNmapa`.`geodatos`
		SET 							
			`zz_borrada`='1'
		WHERE
			id = '".$RID."'
			AND id_actividades='".$Actividad['id']."'
			AND `id_usuarios`='".$UsuarioI."'
	";
	
	$Consulta = mysql_query($query,$Conec1);

	echo mysql_error($Conec1);
	echo "<p>localizacion borrada:</p>";
	echo "
		<script type='text/javascript'>
		parent.window.location='./actividad.php?actividad=".$Actividad['id']."';
		</script>	
	";
		
}elseif($ACCION=="bloquear"&&$Accesos[$UsuarioI]>='2'&&$UsuarioI>0){
	// si el formulario de reingreso así lo definió modifica el registro como borrado
	$query="
		UPDATE 
			`UNmapa`.`geodatos`
		SET 							
			`zz_bloqueado`='1',
			`zz_bloqueadoUsu`='".$UsuarioI."',
			`zz_bloqueadoTx`='".$_POST['bloqTx']."'
		WHERE
			id = '".$RID."'
			AND id_actividades='".$Actividad['id']."'
	";
	
	$Consulta = mysql_query($query,$Conec1);

	echo mysql_error($Conec1);
	echo "<p>localizacion borrada:</p>";
	echo "
		<script type='text/javascript'>
		parent.window.location='./actividad.php?actividad=".$Actividad['id']."';
		</script>	
	";
			
}elseif($ACCION=="desbloquear"&&$Accesos[$UsuarioI]>='2'&&$UsuarioI>0){
	// si el formulario de reingreso así lo definió modifica el registro como borrado
	$query="
		UPDATE 
			`UNmapa`.`geodatos`
		SET 							
			`zz_bloqueado`='0',
			`zz_bloqueadoUsu`='',
			`zz_bloqueadoTx`=''
		WHERE
			id = '".$RID."'
			AND id_actividades='".$Actividad['id']."'
			AND zz_bloqueadoUsu='".$UsuarioI."'
	";
	
	$Consulta = mysql_query($query,$Conec1);

	echo mysql_error($Conec1);
	echo "<p>localizacion borrada:</p>";
	echo "
		<script type='text/javascript'>
		parent.window.location='./actividad.php?actividad=".$Actividad['id']."';
		</script>	
	";
			
}elseif($ACCION=="cambia"&&$Accesos[$UsuarioI]>='2'&&$UsuarioI>0){
	// si el formulario de reingreso así lo definió modifica el registro como borrado
	$query="
		UPDATE 
			`UNmapa`.`geodatos`
		SET 							
			`x`='".$_POST['x']."',
			`y`='".$_POST['y']."',
			`geometria`='".$_POST['geometria']."',
			`fecha`='$HOY',
			`z`='".$_POST['z']."'
			
		WHERE
			id = '".$RID."'
			AND id_actividades='".$Actividad['id']."'
			AND `id_usuarios`='".$UsuarioI."'
	";
	
	
	$Consulta = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);
	//echo "<p>localizacion modificada: $query</p>";
	
		
	if($_POST['nuevacategoria']!=''){
					
		foreach($Actividad['ACTcategorias'] as $cat){
			$n=str_replace(" ", "",$cat['nombre']);
			$n=str_replace(".", "",$n);
			$n=str_replace("-", "",$n);
			$n=strtolower($str);
			
			$v=strtolower($_POST['nuevacategoria']);
			
			if($v==$n){
				$abortar='si';
				$abortarPorId=$cat['id'];
			}
		}

		if($abortar=='si'){
			echo "creación de categoría abortada por nombre existente";
			$_POST['categoria']=$abortarPorId;
		}else{
			$query="
				INSERT INTO 
					`UNmapa`.`ACTcategorias`
					SET
						`id_p_actividades_id`='".$Actividad['id']."',
						`nombre`='".$_POST['nuevacategoria']."',
						`descripcion`='".$_POST['nuevacategoria']."'
				";			
			$Consulta = mysql_query($query,$Conec1);
			$_POST['categoria']=mysql_insert_id($Conec1);	
			echo "<div class='aux'>";
				echo mysql_error($Conec1);
				echo "categoría creada $ncID";
			echo "</div>";
		}	
	}	

	$query="
		UPDATE 
			`UNmapa`.`atributos`
		SET 							
			`valor`='".$_POST['valor']."',
			`categoria`='".$_POST['categoria']."',
			`texto`='".$_POST['texto']."',
			`textobreve`='".$_POST['textobreve']."',
			`link`='".$_POST['link']."',
			`fecha`='".$HOY."',
			`escala`='".$_POST['escala']."',
			`nivelUsuario`='".$Usuario['nivel']."',
			`areaUsuario`='".$Usuario['area']."'
		WHERE
			id = '".$RID."'
			AND id_actividades='".$Actividad['id']."'
			AND `id_usuarios`='".$UsuarioI."'
	";
	
	$Consulta = mysql_query($query,$Conec1);

	echo mysql_error($Conec1);
	
	echo "<script type='text/javascript'>";
	echo "parent.window.location='./actividad.php?actividad=".$Actividad['id']."';";
	echo "</script>";

	echo "<p>atributos modificados: $query</p>";
	break;	
}


	

	if($Actividad['zz_PUBLICO']!='1'&&$Actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){
		echo "<h2>Error en el acceso, esta actividad no se encuentra aún publicada y usted no se encuentra registrado como autor de la misma.</h2>";
		break;
	}
	
	
	foreach($_GET['filtrosi'] as $fdat){		
		$e=explode("__",$fdat);
		$a[$e[0]]=$e[1];
		$filtrosi[]=$a;	
	}
	foreach($_GET['filtrose'] as $fdat){		
		$e=explode("__",$fdat);
		$a[$e[0]]=$e[1];
		$filtrose[]=$a;	
	}		
	
	if(count($filtrosi)>0||count($filtrosi)>0){
		$Registro=0;	
	}
	//echo "<pre>";print_r($Actividad);"</pre>";
	$Puntos = obtenerPuntos($Actividad['id'],$Escala,$Actividad['nivel'],$Actividad['marco'],$filtrose,$filtrosi);//de Baseconsultas.php
	//echo "<pre>";print_r($Puntos);"</pre>";
	// esta aplicación utiliza las librerías de openlayers para mostrar un mapa interactivo que permite definir las localizaciónes
	
	
	
	if($Registro>0){
		foreach($Puntos['Activ'] as $LAid => $LAdata ){
			if($LAdata['id']==$Registro){$rLAid=$LAid;}
			$Prid=$Puntos['Activ'][$rLAid];			
		}
		if($Prid['id_usuarios']==$UsuarioI){
			$Edicion='activa';
		}else{
			$Edicion="inactiva";
		}		
	}else{
		$Edicion='creacion';
	}
	
	
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
	        var mapa, capa, marcklayer, vectorLayer, areaLayer, baseLayer, nuevoLayer,  tempLayer, controls, drawPoint, point_style, _Area, resaltadoLayer;
	        
	        var _selectocupado='no';
	        
	        var _Zoom=undefined;//variable que define el nivel de zun a utilizar en el map. 0: ajustar al (area).

	        // Definimos una función que arranque al cargar la página
	        window.onload= function() {
	        	
	        	OpenLayers.Map.isValidZoomLevel = function(zoomLevel) {
				   return ( (zoomLevel != null) &&
				      (zoomLevel >= 0) && // set min level here, could read from property
				      (zoomLevel < this.getNumZoomLevels()) );
				}
	        	
	        	
	            // Creamos el mapa
	            mapa = new OpenLayers.Map("divMapa", {
				        controls: [
				          new OpenLayers.Control.Navigation(),
				            new OpenLayers.Control.ArgParser(),
				            new OpenLayers.Control.Attribution(),
				            new OpenLayers.Control.LayerSwitcher()
				        ],
				        maxResolution:0.07578125
					});
				
				//mapa.controls.LayerSwitcher.dataLayersDiv.style.display='none';
					
				//a= new OpenLayers.Control.Navigation();
				//mapa.addControl(a);
				
				/*
	            // Creamos una capa fondo con un servicio wms
	            var capa = new OpenLayers.Layer.WMS( 
	                "Base de calles OSM",	                
	                "http://full.wms.geofabrik.de/web/975d3dc24139f06ce8306f9353d28c10?", 
	                {layers: 'basic'},
	                {attribution:"Base OSM bajo servidor GEOFABRIK"}
	            );*/
	            
	            /*
	            // Creamos una capa fondo con un servicio wms
	            var capa = new OpenLayers.Layer.WMS( 
	                "Base de calles OSM",
	                "http://irs.gis-lab.info/?layers=osm&", 
	                {layers: 'basic'},
	                {attribution:"Base OSM bajo servidor GEOFABRIK"}
	            );
			*/
			
			
			
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


           	var styleMapResalt = new OpenLayers.StyleMap({pointRadius: 6,fillColor:'#57f',strokeColor:'#25f', fillOpacity:1,});
           	//creamos capa de puntos resaltados que se llenará solo al resaltar un punto
	        resaltadoLayer = new OpenLayers.Layer.Vector("P. Resaltados", {styleMap: styleMapResalt})





			var layerBaseListeners ={
				
		        featureover: function(e) {
		        	
	        		escrolearB('P_'+e.feature.attributes.id);
					
		          
		            //console.log('Map says: Pointer entered ' + e.feature.id + ' on ' + e.feature.layer.name);
		            _sel=parent.document.getElementById('P_'+e.feature.attributes.id);
		            _sel.className='seleccionado';
		            
					_pp=document.createElement('p');
					_pp.innerHTML=_sel.title;
					_pp.setAttribute('id','desarrollo');
					_sel.appendChild(_pp);
					
					if(_sel.getAttribute('img')!=undefined){
						_pp=document.createElement('img');						
						_pp.setAttribute('src',_sel.getAttribute('img'));
						_pp.setAttribute('id','imagen');
						_sel.appendChild(_pp);
					}
		            
		            //escrolear('P_'+e.feature.attributes.id);
		           //mapa.removeControl(drawPoint);

		
		        },featureout: function(e) {
		        	
		            //e.feature.renderIntent = 'default';
		            //e.feature.layer.drawFeature(e.feature);
		            //console.log('Map says: Pointer left ' + e.feature.id + ' on ' + e.feature.layer.name);
		            parent.document.getElementById('P_'+e.feature.attributes.id).className='';
					
					_pp=_sel.querySelector('#desarrollo');
					_sel.removeChild(_pp);
					if(_sel.getAttribute('img')!=undefined){
						_pp=_sel.querySelector('#imagen');
						_sel.removeChild(_pp);   
					}
		        }
				
			}
			
           	var styleMap = new OpenLayers.StyleMap({pointRadius: 2,});	        
	 		//creamos capa de puntos de la base colectiva seleccionados para acompañar la visualización
	        baseLayer = new OpenLayers.Layer.Vector("P. Compartidos", {styleMap: styleMap,eventListeners: layerBaseListeners});
			//console.log(baseLayer);
			
			var styleMapBloq = new OpenLayers.StyleMap({pointRadius: 4,strokeColor:'#f22',graphicName:'x'});	        
	 		//creamos capa de puntos bloqueados
	        bloqueadoLayer = new OpenLayers.Layer.Vector("P. Retirados", {styleMap: styleMapBloq});

			mapa.events.register("moveend", mapa, function() {
						recargarPuntos();
					});		

			mapa.events.register("zoomend", mapa, function() {
						recargarPuntos();
					});
					
												
					 
			<?php
			
				$minLat=min(180,$Actividad['y0']);
				$minLon=min(180,$Actividad['x0']);
				$maxLat=max(-180,$Actividad['y0']);
				$maxLon=max(-180,$Actividad['x0']);
				
				$minLat=min($minLat,$Actividad['y0']);
				$minLon=min($minLon,$Actividad['x0']);
				$maxLat=max($maxLat,$Actividad['y0']);
				$maxLon=max($maxLon,$Actividad['x0']);
				
				echo "
				var layerListeners = {
				    nofeatureclick: function(e) {
				        //console.log(e.object.name + ' says: No feature clicked.');
				    },
			        featureover: function(e) {
			        	
		        	escrolear('P_'+e.feature.attributes.id);
						
			            e.feature.renderIntent = 'select';
			            e.feature.layer.drawFeature(e.feature);
			            //console.log('Map says: Pointer entered ' + e.feature.id + ' on ' + e.feature.layer.name);
			            parent.document.getElementById('P_'+e.feature.attributes.id).className='seleccionado';
			            //escrolear('P_'+e.feature.attributes.id);
			           //mapa.removeControl(drawPoint);
			           
			            _sel=parent.document.getElementById('P_'+e.feature.attributes.id);
			            _sel.className='seleccionado';
			            
			            if(_sel.querySelector('#desarrollo')!=undefined){
							_pp=_sel.querySelector('#desarrollo');
							_sel.removeChild(_pp);
						}
						if(_sel.querySelector('#imagen')!=undefined){
							_pp=_sel.querySelector('#imagen');
							_sel.removeChild(_pp);   
						}
			            
			            
						_pp=document.createElement('p');
						_pp.innerHTML=_sel.title;
						_pp.setAttribute('id','desarrollo');
						_sel.appendChild(_pp);
						
						if(_sel.getAttribute('img')!=undefined){
							_pp=document.createElement('img');						
							_pp.setAttribute('src',_sel.getAttribute('img'));
							_pp.setAttribute('id','imagen');
							_sel.appendChild(_pp);
						}
			           
			           ";
					   
					   if($_GET['consulta']=='marcarpuntos'||$_GET['consulta']==''){
					   	echo "drawPoint.deactivate();";
					   }
					      
						
			           
				echo "
			        },featureout: function(e) {
			        	
			            e.feature.renderIntent = 'default';
			            e.feature.layer.drawFeature(e.feature);
			            //console.log('Map says: Pointer left ' + e.feature.id + ' on ' + e.feature.layer.name);
			            parent.document.getElementById('P_'+e.feature.attributes.id).className='';
						
						
						
						_pp=_sel.querySelector('#desarrollo');
						_sel.removeChild(_pp);
						
						if(_sel.querySelector('#imagen')!=undefined){
							_pp=_sel.querySelector('#imagen');
							_sel.removeChild(_pp);   
						}
				";
					   
					   
					   if($_GET['consulta']=='marcarpuntos'||$_GET['consulta']==''){
					   	echo "drawPoint.activate();";
			           }
						
				echo "					    
			        },
			        featureclick: function(e) {
			            //console.log('Map says: ' + e.feature.id + ' clicked on ' + e.feature.layer.name);
						//console.log(e.feature.attributes);
						_href='./actividad.php?actividad=' + e.feature.attributes.actividad + '&registro='+ e.feature.attributes.id;
						parent.window.location.assign(_href);						
			        }
					    
				};
				";
				
				
				if($Actividad['categAct']=='1'){
					//definimos una regla de color por categoría
					$uniques='';
					foreach($Actividad['ACTcategorias'] as $cat){
						$uniques.=PHP_EOL."'".$cat['id']."': {strokeColor: '".$cat['CO_color']."',fillColor:'".$cat['CO_color']."'},";
					}
					$uniques=substr($uniques,0,-1).PHP_EOL;
					
					echo "
						var lookup = {
						  ".$uniques."
						}
						
						
						var defaultStyle = new OpenLayers.Style({
						  pointRadius: 6,fillColor:'#f92',strokeColor: '#f92',fillOpacity: '0.2'
						});
						
						var selectStyle = new OpenLayers.Style({
						  pointRadius: 8,fillColor:'#00f',strokeColor:'#00f'
						});
							        
	
						var styleMapCat = new OpenLayers.StyleMap({'default': defaultStyle, 'select': selectStyle});
						styleMapCat.addUniqueValueRules('default', 'categoria', lookup);					
						styleMapCat.styles.default.rules.push(new OpenLayers.Rule({
						    elseFilter: true
						}));
						
						
						//creamos capa de puntos de la actividad
	        			vectorLayer = new OpenLayers.Layer.Vector('P. de Actividad',{styleMap: styleMapCat,eventListeners: layerListeners});
					";
				}else{
					echo  "
						vectorLayer = new OpenLayers.Layer.Vector('P. de Actividad',{eventListeners: layerListeners});
					";
				}

			echo "
			    parent.document.getElementById('puntosdeactividad').innerHTML='';				
				//console.log('".count($Puntos['Activ'])."');
			";
			
				$puntos_a_mostrar_actividad = array_slice($Puntos['Activ'],0,$_SESSION['sigsao']->MAX_VISIBLE_ACTIVITY_POINTS);
				
				
				
				foreach($puntos_a_mostrar_actividad as $LAid => $LAdata ){
					
					if($_GET['filtro']>0){
						if($LAdata['categoria']!=$_GET['filtro']){continue;}
					}	
					
					
					//echo " console.log('".$LAdata['id_usuarios']."'); ";
					
													
					if($LAdata['zz_bloqueado']=='1'&&$Accesos[$UsuarioI]<'2'&&$UsuarioI!=$LAdata['id_usuarios']){continue;}
					
					if($LAdata['zz_bloqueado']=='1'&&($Accesos[$UsuarioI]>='2'||$UsuarioI==$LAdata['id_usuarios'])){			
							echo "
								var point =
									new OpenLayers.Geometry.Point(".$LAdata['lon'].", ".$LAdata['lat'].");
								var pointFeature =
									new OpenLayers.Feature.Vector(point, null);
								pointFeature.attributes = { 'valor' : '".$LAdata['valor']."', 'id': '".$LAdata['id']."', 'categoria': '".$LAdata['categoria']."', 'actividad': '".$LAdata['id_actividades']."'};		
								// Añadir el feature creado a la capa de puntos existentes       
								bloqueadoLayer.addFeatures(pointFeature);	
							";	
					}	
					
					
					if($LAdata['id']==$Registro){$rLAid=$LAid;}
					
					$minLat=min($minLat,$LAdata['lat']);
					$minLon=min($minLon,$LAdata['lon']);
					$maxLat=max($maxLat,$LAdata['lat']);
					$maxLon=max($maxLon,$LAdata['lon']);
					
					if($LAdata['id'] != null) {
						
						$tit = strip_tags($LAdata['texto']);
						
						$tit=eliminarCodigoAsciiAcentos($tit);
								
						echo "
							var _li = parent.document.createElement('li');
							_li.setAttribute('id','P_".$LAdata['id']."');	
							";
							
						if($LAdata['link']!=''){
							if(file_exists($LAdata['link'])){
								echo "_li.setAttribute('img','".$LAdata['link']."');";
							}
						}
							
						echo "
							_li.setAttribute('title','".str_replace("'","",$tit)."');
							_li.setAttribute('onmouseOver','document.getElementById(\"mapa\").contentWindow.activar(\"".$LAdata['id']."\");this.className=\"seleccionado\"');
							_li.setAttribute('onmouseOut','document.getElementById(\"mapa\").contentWindow.desactivar(\"".$LAdata['id']."\");this.className=\"\"');
							
							_mod=document.getElementById('modelopuntoactividad').cloneNode(true);
							_mod.childNodes[0].setAttribute('style','border-color:".$LAdata['categoriaCo'].";background-color:".$LAdata['categoriaCo'].";');
							
													
							var _activ = parent.document.createElement('a');
							_activ.setAttribute('href','./actividad.php?actividad='+'".$LAdata['id_actividades']."&registro=".$LAdata['id']."');
							
							";
							
						echo "	
							_activ.appendChild(_mod);							
						";
							
						
						if($Actividad['valorAct']=='1'){
							echo "
								_activ.innerHTML=_activ.innerHTML+'".$LAdata['valor']." ';
							";
						}
						if($Actividad['categAct']=='1'){
							echo "
								_activ.innerHTML=_activ.innerHTML+'".$LAdata['categoriaTx']." ';
							";
						}
						
						if($LAdata['zz_bloqueado']=='1') {
							echo "
								_activ.innerHTML=_activ.innerHTML+'<span class=\"bloqueado\">x</span>';
							";							
						}		
							
											
						echo "
							_activ.innerHTML=_activ.innerHTML+'<br><span class=\"autor\">".strtoupper($LAdata['Usuario']['nombre']." ".$LAdata['Usuario']['apellido'])."</span>';
							_activ.innerHTML=_activ.innerHTML+'<span class=\"textobreve\">".$LAdata['textobreve']."</span>';
							
							
							_li.appendChild(_activ);
							
							parent.document.getElementById('puntosdeactividad').appendChild(_li);
							
							delete _li;
						";
						
						
						echo "
							var point =
								new OpenLayers.Geometry.Point(".$LAdata['lon'].", ".$LAdata['lat'].");
							var pointFeature =
								new OpenLayers.Feature.Vector(point, null);
							pointFeature.attributes = { 'valor' : '".$LAdata['valor']."', 'id': '".$LAdata['id']."', 'categoria': '".$LAdata['categoria']."', 'actividad': '".$LAdata['id_actividades']."'};		
							// Añadir el feature creado a la capa de puntos existentes       
							vectorLayer.addFeatures(pointFeature);			
						
						//parent.document.getElementById('recuadro3').innerHTML=parent.document.getElementById('recuadro3').innerHTML+'<li>".$LAdata['lon'].", ".$LAdata['lat']." nuevo punto</li>';
						
						";		
					}
				}	
				$lon=($maxLon+$minLon)/2;
				$lat=($maxLat+$minLat)/2;
				


            if($Edicion=='activa'||$Edicion=='creacion'){
					//estilo para puntos en edición
					//echo "console.log('es mio');";
					echo "
						var styleMap2 = new OpenLayers.StyleMap({pointRadius: 4,fillColor:'#f92',strokeColor:'#f00'});
					";           			
            	
            }elseif($Edicion=='inactiva'){
            		//echo "console.log('no es mio".$UsuarioI." v ".$Prid['id_usuarios']."');";					
					//estilo para puntos en visualización
					echo "
	           			var styleMap2 = new OpenLayers.StyleMap({pointRadius: 4,fillColor:'#aaf',strokeColor:'#00f'});
					";
            }
      
        	
    		 //creamos la capa de punto seleccionado
    		echo "
        		nuevoLayer = new OpenLayers.Layer.Vector('P. de Actividad', {styleMap: styleMap2});
        	";
            	
            if($Edicion=='activa'||$Edicion=='inactiva'){
				//creamos el punto seleccionadocapa de puntos del registro seleccionado     		    		
           		
					echo "
					
					
						var point =
					        new OpenLayers.Geometry.Point(".$Prid['lon'].", ".$Prid['lat'].");
					    var pointFeature =
					        new OpenLayers.Feature.Vector(point, null);
						pointFeature.attributes = { 'valor' : '".$Prid['valor']."', 'id': '".$Prid['id']."'};
						
						//console.log(point);		
						
						// Añadir el feature creado a la capa de puntos existentes       
						nuevoLayer.addFeatures(pointFeature);		
						_Zoom ='".min(14,$Puntos['Activ'][$rLAid]['z'])."';
						_CenLon ='".$Prid['lon']."';
						_CenLat ='".$Prid['lat']."';

					";
				
            }			
            ?>
            

           	var styleMap3 = new OpenLayers.StyleMap({pointRadius: 3,fillColor:'#ff2',strokeColor:'#f55'})           	        	
			//creamos capa de puntos temporales antes de verificar si se encuentran dentro del área de trabajo
	        tempLayer = new OpenLayers.Layer.Vector("Selección", {styleMap: styleMap3});
	        
           	var styleMapArea = new OpenLayers.StyleMap({pointRadius: 4,fillColor:'#f92',strokeWidth:0.5,strokeColor:'#b00',fillOpacity:0,graphicName:'cross'})

			//creamos capa de área de trabajo
			areaLayer = new OpenLayers.Layer.Vector("Área de trabajo", {styleMap: styleMapArea});
			
			
			<?php 
			if($Actividad['x0']===null||$Actividad['y0']===null||$Actividad['xF']===null||$Actividad['yF']===null){
				$Actividad['x0']=-57;
				$Actividad['y0']=-58;
				$Actividad['xF']=-35;
				$Actividad['yF']=-36;
			}
			?>
			//creamos area de carga
						
			var _Poly =
		        new OpenLayers.Bounds(<?php echo $Actividad['x0'];?> , <?php echo $Actividad['y0'];?> , <?php echo $Actividad['xF'];?> , <?php echo $Actividad['yF'];?>).toGeometry();

		    var _Area  = [<?php echo $Actividad['x0'];?>, <?php echo $Actividad['yF'];?>, <?php echo $Actividad['xF'];?>,<?php echo $Actividad['y0'];?>];

		    var polyFeature =
		        new OpenLayers.Feature.Vector(_Poly, null);
		        	
		    // añadimos el área a la capa correspondiente
		    areaLayer.addFeatures(polyFeature); 



			<?php 
			
			if($Actividad['imx0']!==null){
				$pad=str_pad($Actividad['id'], 8,'0',STR_PAD_LEFT);
				$ImagenCapa='./documentos/actividades/'.$pad.'/img.png';
				
				$limN=$Actividad['imy0'];
				$limO=$Actividad['imx0'];
				$limS=$Actividad['imyF'];
				$limE=$Actividad['imxF'];
				
				$str_bounds=$limO.",".$limS.",".$limE.",".$limN;
				
				?>
				//var osm = new OpenLayers.Layer.OSM();
	
				var  capaB = new OpenLayers.Layer.Image(
				    'Image',
				    '<?php echo $ImagenCapa;?>',
				    new OpenLayers.Bounds(<?php echo $str_bounds;?>),
				    new OpenLayers.Size(616,320),
				    { isBaseLayer: false,
		              opacity: 0.8,
		              displayOutsideMaxExtent: true
		           }
				);				
							
				<?php 				
			}

			?>
            // Añadimos las capas al mapa
            mapa.addLayers([capa, capaB, areaLayer, resaltadoLayer, vectorLayer, baseLayer, tempLayer, nuevoLayer, bloqueadoLayer]);
            
            mapa.setLayerIndex(resaltadoLayer,99);
            mapa.setLayerIndex(vectorLayer,5);
				
	            // Fijamos centro y zoom
	            //mapa.zoomToMaxExtent();
	   	           
	            if( typeof _Zoom == 'undefined'){	            	
	            	            	
            	    bounds = new OpenLayers.Bounds();
				    bounds.extend(new OpenLayers.LonLat(_Area[0],_Area[1]));
				    bounds.extend(new OpenLayers.LonLat(_Area[2],_Area[3]));
				    bounds.toBBOX(); // returns 4,5,5,6
				    
				    mapa.zoomToExtent(bounds);
            	
				}else{
				
		            console.log(_Area);
		            if(typeof _CenLon == 'undefined' || typeof _CenLat == 'undefined' ){
		            	_CenLon=(_Area[2]-_Area[0])/2+_Area[0];
		            	_CenLat=(_Area[3]-_Area[1])/2+_Area[1];
		            }
	            
	            	mapa.zoomTo(_Zoom);
	            	mapa.setCenter(new OpenLayers.LonLat(_CenLon, _CenLat), _Zoom);
				}                     
	            
	          
	            
	            <?php
	            
	            if($Edicion=='inactiva'){
				echo "
	           		
	 				mapa.addControl(new OpenLayers.Control.MousePosition());
	 				mapa.addControl(new OpenLayers.Control.PanZoomBar());
					
					/*selectPoint=new OpenLayers.Control.SelectFeature( [vectorLayer, baseLayer], {
					    hover: true,
					    onSelect: function (feature) {
					    	//console.log(feature.attributes);
					    	parent.document.getElementById('P_'+feature.attributes.id).className='seleccionado';			
					       //alert('este es el punto con id '+feature.attributes.id);
					        
					    },					
					    onUnselect: function (feature) {
					    	//console.log(feature.attributes);
					    	parent.document.getElementById('P_'+feature.attributes.id).className='';						
					    }
					});			 
					mapa.addControl(selectPoint);			
					selectPoint.activate();*/

		            //drawPoint=new OpenLayers.Control.DrawFeature(recargar,OpenLayers.Handler.Point);		            
					//drawPoint.featureAdded = featAdded;
					//mapa.addControl(drawPoint);
										
					tempLayer.events.register('beforefeatureadded', tempLayer, function (recargar) {
		          	  parent.window.location.reload();
					});
															
					//drawPoint.activate();	
					
					
							
					";
	            }elseif(($Edicion=='activa'||$Edicion=='creacion')&&$_GET['consulta']!='creararea'){
	           		echo "
	           		
	 				mapa.addControl(new OpenLayers.Control.MousePosition());
	 				mapa.addControl(new OpenLayers.Control.PanZoomBar());   

					/*selectPoint=new OpenLayers.Control.SelectFeature( [vectorLayer, baseLayer], {
					    hover: true,
					    onSelect: function (feature) {
					    	//console.log(feature.attributes);
					    	parent.document.getElementById('P_'+feature.attributes.id).className='seleccionado';			
					       //alert('este es el punto con id '+feature.attributes.id);
					        
					    },					
					    onUnselect: function (feature) {
					    	//console.log(feature.attributes);
					    	parent.document.getElementById('P_'+feature.attributes.id).className='';						
					    }
					});			 
					mapa.addControl(selectPoint);				
					selectPoint.activate();*/

		            drawPoint=new OpenLayers.Control.DrawFeature(tempLayer,OpenLayers.Handler.Point);		            
					drawPoint.featureAdded = featAdded;
					mapa.addControl(drawPoint);
										
					tempLayer.events.register('beforefeatureadded', tempLayer, function (limpiar) {
					
		            tempLayer.removeAllFeatures();
					});
															
					drawPoint.activate();	
					
					
							
					";
				}elseif($_GET['consulta']=='creararea'){
						
	            echo "
	            
	            	mapa.addControl(new OpenLayers.Control.MousePosition());

 					drawPoint=new OpenLayers.Control.DrawFeature(areaLayer,OpenLayers.Handler.Point);
 					drawPoint.featureAdded = recAdded;
					mapa.addControl(drawPoint);
	            
		            areaLayer.events.register('beforefeatureadded', areaLayer, function (limpiar) {
		            areaLayer.removeAllFeatures();
					});
					
					drawPoint.activate();
					";			
					
				}

				?>				
	        }
	
	
	        function featAdded() {
	        					        	
	        	<?php
	        	echo"
	        		_minx='".min($Actividad['x0'],$Actividad['xF'])."';
	           		_maxx='".max($Actividad['x0'],$Actividad['xF'])."';
	           		
	           		_miny='".min($Actividad['y0'],$Actividad['yF'])."';
	           		_maxy='".max($Actividad['y0'],$Actividad['yF'])."';
	        	";
	        	?>
	        	
				if(_minx>drawPoint.handler.point.geometry.x||_miny>drawPoint.handler.point.geometry.y||_maxx<drawPoint.handler.point.geometry.x||_maxy<drawPoint.handler.point.geometry.y){
					alert('solo se pueden cargar puntos dentro del área de trabajo');
					tempLayer.removeAllFeatures();
				}else{
					var _lat = parent.document.getElementById("y");				
					_lat.value=drawPoint.handler.point.geometry.y;
					var _lon = parent.document.getElementById("x");
					_lon.value=drawPoint.handler.point.geometry.x;
					
					var _zoom = parent.document.getElementById("z");
					_zoom.value=mapa.getZoom();
				
					var point =
				        new OpenLayers.Geometry.Point(drawPoint.handler.point.geometry.x, drawPoint.handler.point.geometry.y);
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes     
					nuevoLayer.removeAllFeatures();  
					nuevoLayer.addFeatures(pointFeature);
					
					parent.document.getElementById('inputsubmit').style.display='block';
				}			
				
	        }
	        
	        
	        
	        function limpiarAscii(_var){
	        		_TX=_var;
	        		
					_TX = _TX.replace(/&oacute;/g, "ó");
					_TX = _TX.replace(/&eacute;/g, "é");
					_TX = _TX.replace(/&aacute;/g, "á");
					_TX = _TX.replace(/&Aacute;/g, "Á");
					_TX = _TX.replace(/&Eacute;/g, "É");
					_TX = _TX.replace(/&iacute;/g, "í");
					_TX = _TX.replace(/&Iacute;/g, "Í");					
					_TX = _TX.replace(/&Oacute;/g, "Ó");
					_TX = _TX.replace(/&uacute;/g, "ú");
					_TX = _TX.replace(/&Uacute;/g, "Ú");
					_TX = _TX.replace(/&ntilde;/g, "ñ");
					_TX = _TX.replace(/&Ntilde;/g, "Ñ");
					_TX = _TX.replace(/&nbsp;/g, " ");
					_TX = _TX.replace(/&ndash;/g, "-");
					_TX = _TX.replace(/&ldquo;/g, "`");
					_TX = _TX.replace(/&rdquo;/g, "`");
					_TX = _TX.replace(/&nbsp;/g, " ");
					
					_TX = _TX.replace(/(<([^>]+)>)/ig,"");
					_TX = _TX.replace(/\r\n/g, " ");
					_TX = _TX.replace(/\r/g, " ");
					_TX = _TX.replace(/\n/g, " ");
					
					return _TX;
	        }
	        
	        //funcion para dibujar el área de trabajo
	        _tog=0;
	        function recAdded(){
	        	
	        	if(_tog==0){
	        		_tog=1;
	        		_coordy0=drawPoint.handler.point.geometry.y;
	        		_coordx0=drawPoint.handler.point.geometry.x;
	        		
					areaLayer.removeAllFeatures();
					
					var point =
				        new OpenLayers.Geometry.Point(_coordx0, _coordy0);
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes       
					areaLayer.addFeatures(pointFeature);			

	        		
	        	}else if(_tog==1){
	        		//document.getElementById('ref').value='guardar nuevo recorte';	
	        		_tog=0;
	        		_coordy1=drawPoint.handler.point.geometry.y;
	        		_coordx1=drawPoint.handler.point.geometry.x;	
	        		
	        		areaLayer.removeAllFeatures();
	        		
		        	var npoly =
				     new OpenLayers.Bounds(_coordx0,_coordy0 , _coordx1, _coordy1).toGeometry();
				    var npolyFeature =
				       new OpenLayers.Feature.Vector(npoly, null);	
				        		
					// Aï¿½adir el feature creado a la capa de puntos existentes       
					areaLayer.addFeatures(npolyFeature);
					
					parent.document.getElementById("C_x0").value=_coordx0;
					parent.document.getElementById("C_x0").className='cambiado';					
					parent.document.getElementById("C_y0").value=_coordy0;		
					parent.document.getElementById("C_y0").className='cambiado';													
					parent.document.getElementById("C_xF").value=_coordx1;
					parent.document.getElementById("C_xF").className='cambiado';									
					parent.document.getElementById("C_yF").value=_coordy1;	
					parent.document.getElementById("C_yF").className='cambiado';		
	        		        		
	        	}
		}

		function recargarPuntos() {
			zoom = mapa.getZoom();
			geo = mapa.getExtent().toGeometry().getBounds().toArray();
			x0 = geo[0];
			xf = geo[2];
			y0 = geo[1];
			yf = geo[3];
			
			//point.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
			actividad = parent.document.getElementById("actividad").value;
			
			/*console.log('actividad: '+ actividad + 
						' zoom: '+ zoom + 
						' x0: '+ x0 + 
						' xf: '+ xf + 
						' y0: '+ y0 + 
						' yf: '+ yf);
			*/			
			obtienePuntos(actividad, zoom, x0, xf, y0, yf);
		}
		
		function obtienePuntos(actividad, zoom, x0, xf, y0, yf) {
			var parametros = {
					"actividad" : actividad,
					"z" : zoom,
					"x0" : x0,
					"xf" : xf,
					"y0" : y0,
					"yf" : yf,
					"tipo" : 1					
			};
			/*
			//Llamamos a los puntos de la actividad
			$.ajax({
					data:  parametros,
					url:   'puntos_ajax.php',
					type:  'post',
					success:  function (response) {
							procesarRespuestaPuntos(response, 1);
					}
			});
			*/
			parametros["tipo"] = 2;
			//Llamamos a los puntos de otras actividades
			$.ajax({
					data:  parametros,
					url:   'puntos_ajax.php',
					type:  'post',
					success:  function (response) {
							procesarRespuestaPuntos(response, 2);
					}
			});
		}
		
		function procesarRespuestaPuntos(response, tipo) {
			
			if(response!=''){
				var res = $.parseJSON(response);
				
				var layer;
				var divPuntos;
				var activar = "activar";
				var desactivar = "desactivar";
				if (tipo == 1) {
					layer = vectorLayer;
					divPuntos = parent.document.getElementById('puntosdeactividad');
				}
				else {
					layer = baseLayer;
					divPuntos = parent.document.getElementById('puntosdebase');
					activar += 'B';
					desactivar += 'B';
	
				}
				
				layer.removeAllFeatures();
				divPuntos.innerHTML = "";
				
				for (i = 0; i < res.length; i++) {
					if (res[i].id != null) {
						var point = new OpenLayers.Geometry.Point(res[i].x, res[i].y);
						var pointFeature = new OpenLayers.Feature.Vector(point, null);
						pointFeature.attributes = { 'valor' : res[i].valor, 'id': res[i].id, 'categoria': res[i].categoria};	
						
						// Añadir el feature creado a la capa de puntos existentes       
						layer.addFeatures(pointFeature);	
						
						var _li = parent.document.createElement('li');
						_li.setAttribute('id','P_'+res[i].id);
						
						
						_TX=limpiarAscii(res[i].texto);						
						_li.setAttribute('title',_TX);
						
						_li.setAttribute('onmouseOver','document.getElementById(\"mapa\").contentWindow.' + activar + '(\"'+res[i].id+'\");this.className=\"seleccionado\"');
						_li.setAttribute('onmouseOut','document.getElementById(\"mapa\").contentWindow.' + desactivar + '(\"'+res[i].id+'\");this.className=\"\"');
						
						_mod=document.getElementById('modelopuntoactividad').cloneNode(true);
						if (tipo == 1)
							_mod.childNodes[0].setAttribute('style','border-color:'+res[i].color+';background-color:'+res[i].color+';');
												
						var _activ = parent.document.createElement('a');
						_activ.setAttribute('href','./actividad.php?actividad=' + res[i].id_actividades + '&registro='+res[i].id);
						
						_activ.appendChild(_mod);	
	
						if(res[i].categAct == 1){
							_activ.innerHTML=_activ.innerHTML + res[i].nombreCategoria;
						}
						else if(res[i].valorAct == 1){
							_activ.innerHTML=_activ.innerHTML+res[i].valor;
						}
								
						_activ.innerHTML=_activ.innerHTML+'<br><span class=\"autor\">' + res[i].nombreUsuario.toUpperCase() + '</span>';
						_activ.innerHTML=_activ.innerHTML+'<span class=\"textobreve\">' + res[i].textobreve + '</span>';
						
						
						
						if(res[i]!=undefined){
							if(res[i].link!=undefined){
								_li.setAttribute('img',res[i].link);
							}
						}
						_li.appendChild(_activ);
						divPuntos.appendChild(_li);
						
						delete _li;
					}
				}
			}
		}
	</script>	
	<script type='text/javascript'>
	
	//marca seleccionado el punto de la capa vectorLayer
	function activar(_id){
		
		//alert('hola');
		for (i = 0; i < vectorLayer.features.length; i++) {
		    
		    if(vectorLayer.features[i]['attributes']['id']==_id){
		    	_clon = vectorLayer.features[i].clone();
				//alert('hola');				
				resaltadoLayer.addFeatures([ _clon ]);		
				vectorLayer.features[i].destroy();    	
		    }
		}
	}
	
	//desmarca seleccionado el punto de la capa vectorLayer
	function desactivar(_id){
		
		//alert('hola');
		for (i = 0; i < resaltadoLayer.features.length; i++) {		    
		    if(resaltadoLayer.features[i]['attributes']['id']==_id){
		    	_clon = resaltadoLayer.features[i].clone();
				// 'Paste' feature into Layer 1
				
				vectorLayer.addFeatures([ _clon ]);	
				resaltadoLayer.removeAllFeatures();	    	
		    }
		}
	}
	
	//marca seleccionado el punto de la capa baseLayer	
	function activarB(_id){		
		//alert('hola');
		for (i = 0; i < vectorLayer.features.length; i++) {
		    
		    if(baseLayer.features[i]!=undefined){
			    if(baseLayer.features[i]['attributes']['id']==_id){
			    	_clon = baseLayer.features[i].clone();
					// 'Paste' feature into Layer 1				
					resaltadoLayer.addFeatures([ _clon ]);		
					baseLayer.features[i].destroy();    	
			    }
		    }
		}
	}	
	
	//desmarca seleccionado el punto de la capa baseLayer		
	function desactivarB(_id){
		for (i = 0; i < resaltadoLayer.features.length; i++) {		    
		    if(resaltadoLayer.features[i]['attributes']['id']==_id){
		    	_clon = resaltadoLayer.features[i].clone();
				// 'Paste' feature into Layer 1
							
				baseLayer.addFeatures([ _clon ]);	
				resaltadoLayer.removeAllFeatures();	    	
		    }
		}
	}	
	

	function escrolear(_id) { 
		if(_id!='no'){
			_sel=window.parent.document.getElementById(_id);
			_lis=window.parent.document.getElementById('puntosdeactividad');
						
			_lis.insertBefore(_sel, _lis.childNodes[0]);
			
			$('#puntosdeactividad', window.parent.document).scrollTop(0);

	    }
	}	
	function escrolearB(_id) { 
		if(_id!='no'){
			_sel=window.parent.document.getElementById(_id);
			_lis=window.parent.document.getElementById('puntosdebase');
						
			_lis.insertBefore(_sel, _lis.childNodes[0]);
			
			$('#puntosdebase', window.parent.document).scrollTop(0);
		
	    }
	}	
	//var intervalID = setInterval(function(){escrolear(_selectocupado);}, 1000);
	</script>	
	</head>
	<body>
	
	<div id="divMapa"></div>
	
	<?php 
	if($_GET['consulta']=='creararea'){
		echo "<div id='auxmapa' class='definirarea'>redefina el área de trabajo en el mapa</div>";
	}elseif($Registro==0){
		echo "<div id='auxmapa' class='marcar'>Marque un nuevo punto en el mapa</div>";
	}else{
		echo "<div id='auxmapa' class='ver'>visualización del punto seleccionado</div>";
	}
	/*
	echo "HOLA·";
	?>
	
	<form method='POST' action='./argumentacionlocalizacion.php' enctype='multipart/form-data'>
	<input type='hidden' value='<?php echo $Actividad;?>' name='argumentacion'>
	<input type='hidden' value='<?php echo $_GET['localizacion'];?>' name='localizacion'>
	<input style='display:none;' type='button' value='confirmo borrar' onclick='this.parentNode.submit();'>
	</form>
	*/
?>	
	<div id='modelos'>
		<div id='modelopuntoactividad'><div class='punto'></div></div>
		
	</div>

</body>

