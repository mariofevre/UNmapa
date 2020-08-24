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

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}

$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';

//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");
?>
<head>
	<title>Panel de control</title>
	<?php 	include("./includes/meta.php");	?>
	
	<link rel="stylesheet" type="text/css" href="./js/ol4.2/ol.css">
	<link rel="stylesheet" type="text/css" href="css/UNmapa.css" >
	<link rel="stylesheet" type="text/css" href="./css/Mapa.css">
	 <style type='text/css'>

	 </style>
	 
</head>	
<body>
	
	<script  type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>
	<script  type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>		

	
	<div id="divMapa"></div>

	<div id='modelos'>
		<div id='modelopuntoactividad'><div class='punto'></div></div>
	</div>
		
<?php 
	if(!isset($Registro)){$Registro=0;}
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
		exit;
	}
	
	if(!isset($_GET['rid'])){$_GET['rid']='0';}
	if($_GET['rid']>0){
		$Registro=$_GET['rid'];
	}
	
	if(!isset($_GET['filtrosi'])){$_GET['filtrosi']=array();}	
	$filtrosi=array();
	foreach($_GET['filtrosi'] as $fdat){		
		$e=explode("__",$fdat);
		$a[$e[0]]=$e[1];
		$filtrosi[]=$a;	
	}
	if(!isset($_GET['filtrose'])){$_GET['filtrose']=array();}
	$filtrose=array();		
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
		_this.className='seleccionado';
		_stat='cargando';
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

	        
		if(_this.querySelector('#desarrollo')!=undefined){
			_pp=_this.querySelector('#desarrollo');
			_this.removeChild(_pp);
		}
		if(_this.querySelector('#imagen')!=undefined){
			_pp=_this.querySelector('#imagen');
			_this.removeChild(_pp);   
		}
        
		_pp=document.createElement('p');
		_pp.innerHTML=_this.title;
		_pp.setAttribute('id','desarrollo');
		_this.appendChild(_pp);

		if(_this.getAttribute('img')!=undefined){
			_pp=document.createElement('img');						
			_pp.setAttribute('src',_this.getAttribute('img'));
			_pp.setAttribute('id','imagen');
			_this.appendChild(_pp);
		}
			
	}
	
	//desmarca seleccionado el punto de la capa vectorLayer
	function desactivar(_this){
		
		_id=_this.getAttribute('rid');		
		_this.className='';
		
		_features=_sResalt.getFeatures();
		for (i = 0; i < _features.length; i++) {
			_this.className='';		   
		    if(_features[i].getProperties().id==_id){
		    	 //console.log(_features[i].getProperties().id+" vs "+_id);
				_sResalt.removeFeature(_features[i]);	
				if(_this.querySelector('#desarrollo')!=undefined){
					_pp=_this.querySelector('#desarrollo');
					_this.removeChild(_pp);
				}
				if(_this.querySelector('#imagen')!=undefined){
					_pp=_this.querySelector('#imagen');
					_this.removeChild(_pp);   
				}
		    }
		}		
		
		
	}
	
	//marca seleccionado el punto de la capa baseLayer	
	function activarB(_id){		
		//alert('hola');
		resaltadoLayer.getSource().clear();
		  	
		for (i = 0; i < baseLayer.getSource().getFeatures().length; i++) {
			parent.document.querySelector('#puntosdebase > li#P_'+baseLayer.getSource().getFeatures()[i].getProperties().id).removeAttribute('class');
		    if(baseLayer.getSource().getFeatures()[i]!=undefined){
			    if(baseLayer.getSource().getFeatures()[i].getProperties().id==_id){
			    	_clon = baseLayer.getSource().getFeatures()[i].clone();
					// 'Paste' feature into Layer 1				
					resaltadoLayer.getSource().addFeature( _clon);	
					parent.document.querySelector('#puntosdebase > li#P_'+_id).setAttribute('class','seleccionado');  	
			    }
		    }
		}
	}	
	
	//desmarca seleccionado el punto de la capa baseLayer		
	function desactivarB(_id){
		for (i = 0; i < resaltadoLayer.getSource().getFeatures().length; i++) {		    
		    if(resaltadoLayer.getSource().getFeatures()[i].getProperties().id==_id){
		    	_clon = resaltadoLayer.getSource().getFeatures()[i].clone();
				// 'Paste' feature into Layer 1		
				baseLayer.getSource().addFeature(_clon);	
				resaltadoLayer.getSource().clear();	    	
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


    var styleMapResalt = new ol.style.Style({
	     image: new ol.style.Circle({
		    radius: 7,
		    stroke: _yStroke
		 })
    });
   

	    
   /* var cResBase = new ol.style.Style({
	     image: new ol.style.RegularShape({
	      fill: _yFill,
	      stroke: _yStroke,
	      points: 4,
	      radius: 10,
	      radius2: 0,
	      angle: 0,
	    })
    });*/
    
	var cResBase = new ol.style.Circle({
	    radius: 3,
	    fill: _yFill,
	    stroke: new ol.style.Stroke({color : 'rgba(0,100,255,0.8)',width : 1,})
	});
    var styleMapResaltBase = new ol.style.Style({
	     image:cResBase
    });
    
    var styleDef = new ol.style.Style({
	     image:	new ol.style.Circle({
				    radius: 5,
				    fill: _yFill,
				    stroke: _yStroke
				})
    });
    
    var styleBase = new ol.style.Style({
	     image:	new ol.style.Circle({
			    radius: 2,
			    fill:  new ol.style.Fill({color: 'rgb(0,0,0)'})
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

	var _sourceBase = new ol.source.Vector({          
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
        
        var featureBase = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
        	//console.log(layer.get('name'));
	        if(layer.get('name')=='base general'){
	          return feature;
	        }
        });
        
        if(feature!=undefined){
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
	
			if(_sel.getAttribute('img')!='undefined'&&_sel.getAttribute('img')!=undefined){
				_pp=document.createElement('img');					
				_pp.setAttribute('src',_sel.getAttribute('img'));
				_pp.setAttribute('id','imagen');
				_sel.appendChild(_pp);
			}
		}
		
                
        if(featureBase!=undefined){
        	activarB(featureBase.getProperties().id);   	
        }else{
       
        }
        
    }
 
	var _cargado='no';
	
	var listenerKey = _source.on('change', function(e){	
		
		if(_cargado=='si'){return;}
		if (_source.getState() == 'ready') {
		  	
		  	parent._mapainicialCargado='si';		  	
		  	if(parent._actividadConsultada=='si'){
		  		//mostrarArea(parent._Adata);
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
		     	 	

				if(
					_features[_nn].getProperties().xPsMerc==''
					||
					_features[_nn].getProperties().yPsMerc==''
				){
					id=_features[_nn].getProperties().id;
					_coo=_features[_nn].getGeometry().getCoordinates();
					xPsMerc=_coo[0];
					yPsMerc=_coo[1];
					actualizarCoorPsMerc(
						id,
						xPsMerc,
						yPsMerc
					)
				}
		     	//genera un registro de cada punto en el listado activos
		     	
		     	var _li = parent.document.createElement('li');
				_li.setAttribute('id','P_'+_features[_nn].getProperties().id);
				
				
				_TX=limpiarAscii(_features[_nn].getProperties().texto);						
				_li.setAttribute('title',_TX);
				_li.setAttribute('rid',_features[_nn].getProperties().id);
				
				_li.setAttribute('onmouseOver','document.getElementById("mapa").contentWindow.activar(this)');				
				_li.setAttribute('onmouseOut','document.getElementById("mapa").contentWindow.desactivar(this)');
				_li.setAttribute('onclick','_stat="cargando";document.getElementById("mapa").contentWindow.consultaPuntoAj('+_features[_nn].getProperties().id+')');
				
				_mod=document.getElementById('modelopuntoactividad').cloneNode(true);
				
				_mod.querySelector('.punto').setAttribute('style','border-color:'+_features[_nn].getProperties().style.color+';background-color:'+_rell+';');
										
				var _activ = parent.document.createElement('a');
				
				_activ.appendChild(_mod);	
				
				
				//console.log(_features[_nn].getProperties());
				if(_features[_nn].getProperties().categAct == 1){
					_activ.innerHTML+='<span id="categoriatx">'+_features[_nn].getProperties().categoriaNom+'</span><span id="valortx" style="display:none;"></span>';
				}else if(_features[_nn].getProperties().valorAct == 1){
					_activ.innerHTML='<span id="valortx">'+_activ.innerHTML+_features[_nn].getProperties().valor+'</span><span id="categoriatx" style="display:none;"></span>';
				}
				
				if(_features[_nn].getProperties().zz_bloqueado=='1'){
					_activ.innerHTML+='<span class="bloqueado">x</span>';
				}
						
				_activ.innerHTML=_activ.innerHTML+'<br><span class=\"autor\">' + _features[_nn].getProperties().nombreUsuario.toUpperCase() + '</span>';
				_activ.innerHTML=_activ.innerHTML+'<span class=\"textobreve\">' + _features[_nn].getProperties().textobreve + '</span>';
						
				if(_features[_nn].getProperties()!=undefined){
					if(_features[_nn].getProperties().link!=undefined){
						if(_features[_nn].getProperties().linkth!=undefined){
							_li.setAttribute('img',_features[_nn].getProperties().linkth);
						}
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

	var baseLayer = new ol.layer.Vector({
		name: 'base general',
		style: styleBase,
	    source: _sourceBase
	});
		
	var bloquedoLayer = new ol.layer.Vector({
		name: 'bloqueadoLayer',
	    source: _sBloqueado
	});
	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResaltBase,
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
		name: 'areaLayer',
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
	      candidatoLayer,
	      cargadoLayer,
	      baseLayer,
	      resaltadoLayer,
	      areaLayer
	    ],
	    target: 'divMapa',
	    view: _view
	});
	 
	 
	if(parent._Adata.x0!==""||parent._Adata.geometria!==""){ 
		mostrarArea(parent._Adata);
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
	
	mapa.on("moveend", function() {
		recargarPuntos();
	});	

	/*mapa.on("zoomend", function() {
		recargarPuntos();
	});*/
	
	function recargarPuntos() {
		console.log('funcion: recargarPuntos');
		zoom=mapa.getView().getZoom();
		res=mapa.getView().getResolution();
		console.log(zoom);
		//console.log(res);
		//zoom = mapa.getZoom();
		geo = mapa.getView().calculateExtent();
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
		console.log('funcion: obtinenePuntos');
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
			url:   './puntos_ajax.php',
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
				if(layer.getSource()!=null){
					layer.getSource().clear();
				}
				divPuntos.innerHTML = "";
			
				for (i = 0; i < res.length; i++) {
					if (res[i].id != null) {
						var point = new ol.geom.Point([res[i].xPsMerc, res[i].yPsMerc]);
						var pointFeature = new ol.Feature({geometry:point});
						pointFeature.setId(res[i].id);
						pointFeature.setProperties({ 'valor' : res[i].valor, 'id': res[i].id, 'categoria': res[i].categoria});	
						
						// Añadir el feature creado a la capa de puntos existentes       
						layer.getSource().addFeature(pointFeature);	
						
						//console.log(pointFeature.getGeometry());
							
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
						_activ.setAttribute('target','_blank');
						
						_activ.appendChild(_mod);	
	
						if(res[i].categAct == 1){
							_activ.innerHTML=_activ.innerHTML + res[i].nombreCategoria;
						}
						else if(res[i].valorAct == 1){
							_activ.innerHTML=_activ.innerHTML+res[i].valor;
						}
						
						_activ.innerHTML=_activ.innerHTML+'<br><span class=\"autor\">' + res[i].nombreUsuario.toUpperCase() + '</span>';
						
						_tx=res[i].textobreve;
						
						//console.log(res);
						if(res[i].textobreve == ''){
							_tx=_TX.substring(0,30);
						}	
						
						_activ.innerHTML=_activ.innerHTML+'<span class=\"textobreve\">' + _tx + '</span>';
						
						if(res[i].linkth!=undefined){
							_activ.innerHTML=_activ.innerHTML+'<img src=\"'+ res[i].linkth +'\">';
						}
						
						
						
						
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
				//console.log(baseLayer.getSource().getFeatures());
			}
			
			if(parent._IdReg!=''){
				consultaPuntoAj(parent._IdReg);
			}
		}		
	function mostrarArea(_ac){
		
		_features=_sArea.getFeatures();
		for (i = 0; i < _features.length; i++) {_sArea.removeFeature(_features[i]);}	
			
		if(_ac.geometria!=''){
			
	        var format = new ol.format.WKT();	
	        var _feat = format.readFeature(_ac.geometria, {
	            dataProjection: 'EPSG:4326',
	            featureProjection: 'EPSG:3857'
	        });
	        _r=_feat.getGeometry();

	        
		}else{
		
			_ext= [	parseFloat(_ac.x0),	parseFloat(_ac.y0),	parseFloat(_ac.xF),	parseFloat(_ac.yF)]
			_r = new ol.geom.Polygon.fromExtent(_ext);
			_r.transform('EPSG:4326', 'EPSG:3857');
			_feat = new ol.Feature({
			    name: "Area de Trabajo",
			    geometry: _r
			});
		}
		
		_sArea.addFeature(_feat);
		
		if(parent._Pdat==undefined){
			_view.fit(_r);
		}
		
	}
	
	function consultaPunto(pixel,_ev){
	    
		var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	       if(layer.get('name')=='vectorLayer'){
	          return feature;
	       }
	    });
	    
	   
	    _enarea = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
		      if(layer.get('name')=='areaLayer'){
		          return true;
		      }
	     }); 
	     if(_enarea==undefined){ _enarea = false;}
	     
	   	  
	    _features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
	     
	    if(feature==undefined){
	    	
	    	if(parent._Aactiva=='si'){
	    		
	    		if(!_enarea){
			   		if(!confirm('Este punto está fuera del área de trabajo propuesto! \n ¿Continuar de todos modos?')){return;}
			   	}
			   	
	    		_features=_sCargado.getFeatures();	
				for (i = 0; i < _features.length; i++) {		
					_sCargado.removeFeature(_features[i]);
				}
				
				
			   	
	    		_res=Array();
	    		_res.data=Array();
	    		_res.data.nuevo=Array();
	    		
	    		_coord3857=mapa.getCoordinateFromPixel(pixel);
	    		_r = new ol.geom.Point(_coord3857);
	    		
	    		
	    		_coord=ol.proj.transform(_coord3857,'EPSG:3857', 'EPSG:4326');
	    		
				_fr = new ol.Feature({
				    name: "Candidato",
				    geometry: _r
				});
	    		_sCandidato.addFeature(_fr);
	    		
	    		_view.fit(_fr.getGeometry(), {'duration': 300, 'maxZoom' : _view.getZoom()}); 	    		
	    		
	    		
			   	
	    		_res.data.nuevo={
	    			'x': _coord[1],
	    			'y': _coord[0],
	    			'z': _view.getZoom(),
	    			'xPsMerc':_coord3857[1],
					'yPsMerc':_coord3857[0],
					'zResPsMerc':_view.getResolution()	    			
	    		};
	    		
	    		parent.cargaFormulario(_res);
	    		
	    		
	        }
	        
	    	return;
	    	
	    }
		
		_Pid=feature.getProperties().id;
		 _stat='cargando';
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
		
		mostrarArea(parent._Adata);	
	}
	
	function actualizarCoorPsMerc(id,xPsMerc,yPsMerc){
		
		_datos={
			'id':id,
			'xPsMerc':xPsMerc,
			'yPsMerc':yPsMerc			
		};
		
		$.ajax({
			data: _datos,
			url:   'punto_actualizaPsMerc.php',
			type:  'post',
			success:  function (response){
				console.log($.parseJSON(response));
			}
		})
	}
		
	var _stat='cargando';
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
				
				console.log(_stat);
				if(_stat=='cargando'){
					_view.fit(_clon.getGeometry(), {'duration': 1000, 'maxZoom' : (parseFloat(_res.data.punto.z))} );
					_stat='cargado';
				}
				
			}
		})
	}
</script>


</body>

