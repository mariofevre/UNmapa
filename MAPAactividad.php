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

	<div id='modelos'>
		<div id='modelopuntoactividad'><div class='punto'></div></div>
	</div>
		
<?php 

	if($Registro==0){
		echo "<div id='auxmapa' class='marcar'>Marque un nuevo punto en el mapa</div>";
	}else{
		echo "<div id='auxmapa' class='ver'>visualización del punto seleccionado</div>";
	}
	
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
	//$Puntos = obtenerPuntos($Actividad['id'],$Escala,$Actividad['nivel'],$Actividad['marco'],$filtrose,$filtrosi);//de Baseconsultas.php
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

    
   	_yStroke = new ol.style.Stroke({
		color : 'rgba(0,100,255,0.8)',
		width : 2,
	});
	_yFill = new ol.style.Fill({
	   color: 'rgba(0,100,255,0.6)'
	}); 
	var cRes = new ol.style.Circle({
	    radius: 5,
	    fill: _yFill,
	    stroke: _yStroke
	});
    var styleMapResalt = new ol.style.Style({
	     image:cRes
    });
    
    
    
    var styleDef = new ol.style.Style({
	     image:	new ol.style.Circle({
				    radius: 5,
				    fill: _yFill,
				    stroke: _yStroke
				})
    });

    var styleArea = new ol.style.Style({
	    stroke: new ol.style.Stroke({color : 'rgba(255,50,100,0.8)', width : 1}) 
    });
 
    var styleCandidato = new ol.style.Style({	    
	    image: new ol.style.Circle({ radius: 5,
		    stroke: new ol.style.Stroke({color : 'rgba(255,100,50,1)', width : 1}),
	    	fill: new ol.style.Fill({color : 'rgba(200,250,100,0.5)'}) 
		})
    });
         
 	var _myStroke = new ol.style.Stroke({
		color : 'rgba(255,0,0,1.0)',
		width : 1,
	});
	var circle = new ol.style.Circle({
	    radius: 5,
	    stroke: _myStroke
	});
	 
	var sy = new ol.style.Style ({
	   image:circle
	});

	var _source = new ol.source.Vector({
      url: './puntos_consulta.php?aid='+_Aid,
      format: new ol.format.GeoJSON(),          
      projection: 'EPSG:4326'      
    }); 

	var _sBloqueado = new ol.source.Vector({        
      projection: 'EPSG:4326'      
    }); 
    
        
	var _sResalt = new ol.source.Vector({        
      projection: 'EPSG:4326'      
    }); 
    
    var _sCargado = new ol.source.Vector({        
      projection: 'EPSG:4326'      
    }); 

    var _sCandidato = new ol.source.Vector({        
      projection: 'EPSG:4326'      
    }); 
    		    	    
	var  _sArea = new ol.source.Vector({        
      projection: 'EPSG:4326'      
    }); 
	
	var highlight;
	
    var sobrePunto = function(pixel) {        	
    	_features = _source.getFeatures();
    	    	
    	for(_nn in _features){
    		if(_features[_nn].getProperties().sel=='si'){
    			_features[_nn].getProperties().sel='no'
    			
    			var _myStroke = new ol.style.Stroke({
				   color : _features[_nn].getProperties().style.color,
				   width : 1
				});
				
				_rell=_features[_nn].getProperties().style.color;
				if(_features[_nn].getProperties().categDat==''){_rell='rgb(255,255,255)';}
				
				var circle = new ol.style.Circle({
				    radius: 5,
				    stroke: _myStroke,
				    fill: new ol.style.Fill({color: _rell}) 
				})
				
				var sy = new ol.style.Style ({
				   image:circle
				});
				
		     	_features[_nn].setStyle(sy);	     	
		     	
    		}
    	}
    	
    	_lisis=parent.document.querySelectorAll('#puntosdeactividad > li');
    	for(_nl in _lisis){
    		if(typeof _lisis[_nl] == 'object'){
    			_lisis[_nl].removeAttribute('class');
    		}
    	}
    	
        
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='vectorLayer'){
	          return feature;
	         }
        });
        
        if(feature==undefined){return;}
        //console.log(feature.getProperties());
        
		escrolear('P_'+feature.getProperties().id);
         
		feature.setProperties({sel:'si'});
		feature.setStyle(styleMapResalt);

		parent.document.getElementById('P_'+feature.getProperties().id).className='seleccionado';
       
       	_sel=parent.document.getElementById('P_'+feature.getProperties().id);
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
    }
 
	var _cargado='no';
	
	var listenerKey = _source.on('change', function(e){	
		if(_cargado=='si'){return;}
		if (_source.getState() == 'ready') {
		  	
		  	parent._mapainicialCargado='si';		  	
		  	if(parent._actividadConsultada=='si'){
		  		//mostrarArea(parent._Adat);
		  	}
		  	
		  	//consultaPunto();
		  	_cargado='si';
		    _features=_source.getFeatures();	    
		    
		    for(_nn in _features){

				_rell=_features[_nn].getProperties().style.color;
				if(_features[_nn].getProperties().categDat==''){_rell='rgb(255,255,255)';console.log('si')}
				
				var sy = new ol.style.Style ({
					   image: new ol.style.Circle({
					    radius: 5,
					    fill: new ol.style.Fill({color: _rell,opacity:0.8}),
					    stroke: new ol.style.Stroke({color : _features[_nn].getProperties().style.color,width : 1})
					})
				}); 
		     	_features[_nn].setStyle(sy);	
		     	
		     	if(_features[_nn].getProperties().zz_bloqueado==1){
			     	_clonef=_features[_nn].clone();
			     	_sBloqueado.addFeature(_clonef);
				     
		    		var sX = new ol.style.Style ({
					   image:new ol.style.RegularShape({
				            fill:  new ol.style.Fill({color : 'rgba(200,0,0,1)'}) ,
				            stroke: ol.style.Stroke({color : 'rgba(200,0,0,1)', width : 5}),
				            points: 4,
				            radius: 10,
				            radius2: 2,
				            angle: 0.785398
				        })
					});
			     	_clonef.setStyle(sX);
		     	}
		     	 	
		     	
		     	//genera un registro de cada punto en el listado activos
		     	
		     	var _li = parent.document.createElement('li');
				_li.setAttribute('id','P_'+_features[_nn].getProperties().id);
				
				
				_TX=limpiarAscii(_features[_nn].getProperties().texto);						
				_li.setAttribute('title',_TX);
				_li.setAttribute('rid',_features[_nn].getProperties().id);
				
				_li.setAttribute('onmouseOver','document.getElementById("mapa").contentWindow.activar(this)');				
				_li.setAttribute('onmouseOut','document.getElementById("mapa").contentWindow.desactivar(this)');
				_li.setAttribute('onclick','document.getElementById("mapa").contentWindow.consultaPuntoAj('+_features[_nn].getProperties().id+')');
				
				_mod=document.getElementById('modelopuntoactividad').cloneNode(true);
				
				_mod.querySelector('.punto').setAttribute('style','border-color:'+_features[_nn].getProperties().style.color+';background-color:'+_rell+';');
										
				var _activ = parent.document.createElement('a');
				
				_activ.appendChild(_mod);	
				
				
				//console.log(_features[_nn].getProperties());
				if(_features[_nn].getProperties().categAct == 1){
					_activ.innerHTML=_activ.innerHTML + _features[_nn].getProperties().categoriaNom;
				}else if(_features[_nn].getProperties().valorAct == 1){
					_activ.innerHTML=_activ.innerHTML+_features[_nn].getProperties().valor;
				}
				
				if(_features[_nn].getProperties().zz_bloqueado=='1'){
					_activ.innerHTML+='<span class="bloqueado">x</span>';
				}
						
				_activ.innerHTML=_activ.innerHTML+'<br><span class=\"autor\">' + _features[_nn].getProperties().nombreUsuario.toUpperCase() + '</span>';
				_activ.innerHTML=_activ.innerHTML+'<span class=\"textobreve\">' + _features[_nn].getProperties().textobreve + '</span>';
				
				if(_features[_nn].getProperties()!=undefined){
					if(_features[_nn].getProperties().link!=undefined){
						_li.setAttribute('img',_features[_nn].getProperties().link);
					}
				}
				
				_li.appendChild(_activ);
				
				parent.document.getElementById('puntosdeactividad').appendChild(_li);
				
				delete _li;
					     
		    }
		}
	});
		
	var vectorLayer = new ol.layer.Vector({
		name: 'vectorLayer',
		style: styleDef
	    //source: _source
	});
	
	var bloquedoLayer = new ol.layer.Vector({
		name: 'bloqueadoLayer',
	    source: _sBloqueado
	});
	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sResalt
	});

	var cargadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sCargado
	});

	var candidatoLayer = new ol.layer.Vector({
		style: styleCandidato,
		source: _sCandidato
	});	
	
		
	var areaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea
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
	      vectorLayer,
	      bloquedoLayer,
	      resaltadoLayer,
	      candidatoLayer,
	      cargadoLayer,
	      areaLayer
	    ],
	    target: 'divMapa',
	    view: _view
	});
	 
	 
	if(parent._Adat.x0!==""){ 
		mostrarArea(parent._Adat);
	}	  
	vectorLayer.setSource(_source);
	
	layerOSM.setSource(_sourceBaseOSM);		
	  
	mapa.on('pointermove', function(evt) {
		
        if (evt.dragging) {
        	
        	//console.log(evt);
        	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
  			//deltaY = evt.coordinate[1] - evt.coordinate_[1];
			//console.log(deltaX);
			
          return;
        }
        var pixel = mapa.getEventPixel(evt.originalEvent);

        sobrePunto(pixel);
     });

    mapa.on('click', function(evt){
       consultaPunto(evt.pixel,evt);       
    });

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
	
	function consultaPunto(pixel,_ev){
	    
		var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	       if(layer.get('name')=='vectorLayer'){
	          return feature;
	       }
	    });  
	    	    
	    _features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
	      
	    if(feature==undefined){
	    	
	    	if(parent._Aactiva=='si'){
	    		
	    		_features=_sCargado.getFeatures();	
				for (i = 0; i < _features.length; i++) {		
					_sCargado.removeFeature(_features[i]);
				}
				
	    		_res=Array();
	    		_res.data=Array();
	    		_res.data.nuevo=Array();
	    		
	    		_coord=mapa.getCoordinateFromPixel(pixel);
	    		_r = new ol.geom.Point(_coord);
	    		
	    		_coord=ol.proj.transform(_coord,'EPSG:3857', 'EPSG:4326');
	    		
				_fr = new ol.Feature({
				    name: "Candidato",
				    geometry: _r
				});
	    		_sCandidato.addFeature(_fr);
	    		
	    		_view.fit(_fr.getGeometry(), {'duration': 300, 'maxZoom' : _view.getZoom()}); 	    		
	    		
	    		_res.data.nuevo={
	    			'x': _coord[1],
	    			'y': _coord[0],
	    			'z': _view.getZoom()	    			
	    		};
	    		
	    		parent.cargaFormulario(_res);
	        }
	        
	    	return;
	    	
	    	}
		
		_Pid=feature.getProperties().id;
		consultaPuntoAj(_Pid);
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
	
	function consultaPuntoAj(_Pid){
		
		_datos={};
		_datos["pid"]=_Pid
		_datos["aid"]=_Aid;
		
		$.ajax({
			data: _datos,
			url:   'punto_consulta.php',
			type:  'post',
			success:  function (response){
				
				var _res = $.parseJSON(response);
				//console.log(_res);
				
				parent._Pdat=_res.data.punto;
				
				_Data=_res.data;
				parent.cargaFormulario(_res);			
				
				_features=_sCargado.getFeatures();	
				for (i = 0; i < _features.length; i++) {		
					_sCargado.removeFeature(_features[i]);
				}
				
				_features=_sCandidato.getFeatures();	
				for (i = 0; i < _features.length; i++) {		
					_sCandidato.removeFeature(_features[i]);
				}
				
				_features=_source.getFeatures();
				for (i = 0; i < _features.length; i++) {				   
				    if(_features[i].getProperties().id==_res.data.punto.id){
				    	_clon = _features[i].clone();
				    	_clon.setStyle(styleMapResalt);										
						_sCargado.addFeatures([ _clon ]);
				    }
				}
				
				_view.fit(_clon.getGeometry(), {'duration': 1000, 'maxZoom' : (parseFloat(_res.data.punto.z))} );
				
			}
		})
	}
</script>


</body>

