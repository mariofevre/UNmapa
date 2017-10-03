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
ini_set('display_errors',true);

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

//include("./includes/BASEconsultas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}

$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';

//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");
?>
<head>
	<title>Panel de control</title>
	<?php 	include("./includes/meta.php");	?>
	
	<link rel="stylesheet" type="text/css" href="./js/ol4.2/ol.css">
	
	<link href="css/UNmapa.css" rel="stylesheet" type="text/css">	
	
	<link rel="stylesheet" type="text/css" href="./css/Mapa.css">
	 <style type='text/css'>

	 </style>
	 
</head>	
<body>
	<script src="./js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<script src="./js/ol4.2/ol.js"></script>		

	
	<div id="divMapa"></div>
		
	<script type="text/javascript">
	//fuciones genericas	
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
	</script>


<script type='text/javascript'>
	//funciones de seleccion de features en el mapa
	
	//marca seleccionado el punto de la capa vectorLayer
	
	function activar(_this){
		
		_id=_this.getAttribute('rid');		
		this.className='seleccionado';
		
		_features=_source.getFeatures();
		for (i = 0; i < _features.length; i++) {
		   
		    if(_features[i].getProperties().id==_id){
		    	// console.log(_features[i].getProperties().id+" vs "+_id);
		    	_clon = _features[i].clone();
		    	_clon.setStyle(styleMapResalt);
				//alert('hola');
								
				_sResalt.addFeatures([ _clon ]);
				//_source.removeFeature(_features[i]);
				    	
		    }
		}
	}
	
	//desmarca seleccionado el punto de la capa vectorLayer
	function desactivar(_this){
		
		_id=_this.getAttribute('rid');		
		this.className='';
		
		_features=_sResalt.getFeatures();
		for (i = 0; i < _features.length; i++) {		   
		    if(_features[i].getProperties().id==_id){
		    	 //console.log(_features[i].getProperties().id+" vs "+_id);
				_sResalt.removeFeature(_features[i])				    	
		    }
		}		
	}
	
	//marca seleccionado el punto de la capa baseLayer	
	function activarB(_id){		
		//alert('hola');
		for (i = 0; i < _source.getFeatures.length; i++) {
		    
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
		//console.log(_id);
		
		if(_id!='no'){
			//console.log(_id);
			
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
	    	 
	    	 
	    	 	
<script type="text/javascript">
	
	
	
	var _Aid = '<?php echo $ActividadId;?>';
        
    var styleArea = new ol.style.Style({
	    stroke: new ol.style.Stroke({color : 'rgba(255,50,100,0.8)', width : 1}) 
	    
    });
    	    	    
	var _cargado='no';
	
	var _sArea = new ol.source.Vector({wrapX: false, projection: 'EPSG:4326'});
	var _sPreArea = new ol.source.Vector({wrapX: false, projection: 'EPSG:4326'});
	

	var AreaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea
	});
	
	var preAreaLayer = new ol.layer.Vector({		
		source: _sPreArea
	});
	
	var _view =	new ol.View({
      projection: 'EPSG:3857',
      center: [0, 0],
      zoom: 3,
      minZoom:2,
      maxZoom:19	      
	});
      
	var _sourceBaseOSM=new ol.source.OSM();
	
	var _sourceBaseBING=new ol.source.BingMaps({
		 	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
		 	imagerySet:  'Aerial'
		});
	
	var layerOSM = new ol.layer.Tile({
		 
	});
	
	var layerBing = new ol.layer.Tile({
		 
	});	
	
	var mapa = new ol.Map({
	    layers: [
	      layerOSM,
	      layerBing,
	      AreaLayer,
	      preAreaLayer
	    ],
	    target: 'divMapa',
	    view: _view
	});
	  
	if(parent._Adat.x0!==""){ 
		mostrarArea(parent._Adat);
	}	  
		  
	
	layerOSM.setSource(_sourceBaseOSM);
	
	var _dibujando='no';
	
	var draw; // global so we can remove it later
    function addInteraction() {
    	var geometryFunction;
    	_dibujando='si';
    	value = 'Circle';
        geometryFunction = ol.interaction.Draw.createBox();
        draw = new ol.interaction.Draw({
            source: _sPreArea,
            type: /** @type {ol.geom.GeometryType} */ (value),
            geometryFunction: geometryFunction
        });
        mapa.addInteraction(draw);
   
	}
	
	
	_carga='no';
	_sPreArea.on('change', function(evt){	
		
		console.log(_carga);	
		if(_carga=='si'){return;}
		
		_features=_sArea.getFeatures();
		for (i = 0; i < _features.length; i++) {_sArea.removeFeature(_features[i]);}
		
		
		_features=_sPreArea.getFeatures();
		
		_carga='si';
		_sArea.addFeature(_features[0].clone());
		_features=_sPreArea.getFeatures();
		for (i = 0; i < _features.length; i++) {_sPreArea.removeFeature(_features[i]);}		
		_carga='no';
		
		_cu=_features[0].getGeometry().transform('EPSG:3857', 'EPSG:4326');
		_co=_cu.getExtent();			
		parent.document.getElementById('C_x0').value=_co[0];
		parent.document.getElementById('C_y0').value=_co[1];
		parent.document.getElementById('C_xF').value=_co[2];
		parent.document.getElementById('C_yF').value=_co[3];
				    
    });
    
	addInteraction();
      

	_view.on('change:resolution', function(evt){       
        if(_view.getZoom()>=19){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.8);
       }else if(_view.getZoom()>=17){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.5);
       }else{
       		layerBing.setSource();
       }
    });
	
	function mostrarArea(_ac){
		
		_features=_sArea.getFeatures();
		for (i = 0; i < _features.length; i++) {_sArea.removeFeature(_features[i]);}	
			
		//_r = new ol.geom.Polygon([[-66,-44],[-1,-1], [-66,-1],[-1,-44]],'XY');
		_ext= [	parseFloat(_ac.x0),	parseFloat(_ac.y0),	parseFloat(_ac.xF),	parseFloat(_ac.yF)]
		_r = new ol.geom.Polygon.fromExtent(_ext);
		_r.transform('EPSG:4326', 'EPSG:3857');
		_fr = new ol.Feature({
		    name: "Area de Trabajo",
		    geometry: _r
		});
		_sArea.addFeature(_fr);
		
		if(parent._Pdat==undefined){_view.fit(_r)}
		
	}
	
	function reiniciarMapa(){
		_features=_sCargado.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCargado.removeFeature(_features[i]);
		}
		
		_features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
		
		mostrarArea(parent._Adat);	
	}
	
</script>


</body>

