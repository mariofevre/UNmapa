<?php
/**
* encabezado.php
*
* menu inicial para todas las pantallas del proyecto. Incluye saludo de usuario y barra de busqueda
*  
* @package    	UNmapa Herramienta pedágogica para la construccion colaborativa del territorio.  
* @subpackage 	actividad
* @author     	Universidad Nacional de Moreno
* @author     	<mario@trecc.com.ar>
* @author    	https://github.com/mariofevre/UNmapa
* @author		based on proyecto Plataforma Colectiva de Información Territorial: UBATIC2014
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2019 Universidad Nacional de Moreno
* @copyright	esta aplicación deriba de publicaciones GNU AGPL : Universidad de Buenos Aires 2015 / TReCC SA 2014
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

if(isset( $_SESSION['Unmapa'][$CU]->USUARIO['uid'])){

	//print_r($_SESSION['Unmapa'][$CU]);
	?>
	<div class='recuadro' id='recuadro1'>
	<p>hola: <span id="nombreusuario"></span></p>
	<a class='boton' href='./login.php'>cerrar sesión</a>
	<a class='boton' href='./actividades.php'>salir al listado de actividades</a>
	<p>buscar: <input type='text' name='busqueda' onkeyup="tecleaBusqueda(this,event)"></p>
	</div>
	
	
	<script type="text/javascript">
		var _Usuario='<?php echo $_SESSION['Unmapa'][$CU]->USUARIO['usuario']?>';
		document.querySelector('#recuadro1 #nombreusuario').innerHTML=_Usuario;
	</script>
	
	<script type="text/javascript">
		var _Usuario='<?php echo $_SESSION['Unmapa'][$CU]->USUARIO['usuario']?>';
		document.querySelector('#recuadro1 #nombreusuario').innerHTML=_Usuario;
	</script>
	
	
<script type="text/javascript">	
//funciones de filtrado

	function tecleaBusqueda(_this,_event){
		if ( 
            _event.keyCode == '9'//presionó tab no es un nombre nuevo
            ||
            _event.keyCode == '13'//presionó enter
            ||
            _event.keyCode == '32'//presionó espacio
            ||
            _event.keyCode == '37'//presionó direccional
            ||
            _event.keyCode == '38'//presionó  direccional
            ||
            _event.keyCode == '39'//presionó  direccional
            || 
            _event.keyCode == '40'//presionó  direccional		  		
        ){
        	return;
        }
		
		console.log(_event.keyCode);
		if ( 
			_event.keyCode == '27'//presionó tab no es un nombre nuevo
		){
			document.querySelector('[name="busqueda"]').value='';
		}
		_val=document.querySelector('[name="busqueda"]').value;
					
		_hatch=_val.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_hatch=_hatch.replace('/[^A-Za-z0-9\-]/gi', '');
		_hatch=_hatch.replace(/ /g, '');
		_hatch=_hatch.toLowerCase();
		
		filtrarRegs(_hatch);
		filtrarRegsReporte(_hatch);
		//filtrarActs(_hatch);
		//BuscarExternos(_hatch);
	}
	
	function filtrarRegs(_hatch){
		_segs=document.querySelectorAll('#recuadro3 #puntosdeactividad li');
		for(_ns in _segs){
			if(typeof _segs[_ns] != 'object'){continue;}
			
			console.log(_hatch.length);
			if(_hatch.length<2){
				_segs[_ns].setAttribute('filtroB','ver');
				continue;
			}
			
			_st=_segs[_ns].title;
			_st+=_segs[_ns].getAttribute('rid');
			_st+=_segs[_ns].querySelector('#categoriatx').innerHTML;
			_st+=_segs[_ns].querySelector('#valortx').innerHTML;
			_st+=_segs[_ns].querySelector('.autor').innerHTML;
			_st+=_segs[_ns].querySelector('.textobreve').innerHTML;
			
			
			_st=_st.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			_st=_st.replace('/[^A-Za-z0-9\-]/gi', '');
			_st=_st.replace(/ /g, '');
			_st=_st.toLowerCase();
			
			
			//console.log(_hatch+' vs '+_st+' -- '+_st.indexOf(_hatch));
			if(_st.indexOf(_hatch)>=0){
				_segs[_ns].setAttribute('filtroB','vera');
			}else{
				_segs[_ns].setAttribute('filtroB','nover');
			}
					
		}
		
	    
	}
	
	
	function filtrarRegsReporte(_hatch){
		_segs=document.querySelectorAll('.registro');
		for(_ns in _segs){
			if(typeof _segs[_ns] != 'object'){continue;}
			
			if(_segs[_ns].getAttribute('id') == 'modelo'){continue;}
			
			console.log(_hatch.length);
			if(_hatch.length<2){
				_segs[_ns].setAttribute('filtroB','ver');
				continue;
			}
			
			_st =_segs[_ns].querySelector('#rid').innerHTML;
			_st+=_segs[_ns].querySelector('#autor').innerHTML;
			_st+=_segs[_ns].querySelector('#categoriatx').innerHTML;
			_st+=_segs[_ns].querySelector('#textobreve').innerHTML;
			_st+=_segs[_ns].querySelector('#valor').innerHTML;
			_st+=_segs[_ns].querySelector('#texto').innerHTML;
			
			
			_st=_st.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			_st=_st.replace('/[^A-Za-z0-9\-]/gi', '');
			_st=_st.replace(/ /g, '');
			_st=_st.toLowerCase();
			
			
			//console.log(_hatch+' vs '+_st+' -- '+_st.indexOf(_hatch));
			if(_st.indexOf(_hatch)>=0){
				_segs[_ns].setAttribute('filtroB','vera');
			}else{
				_segs[_ns].setAttribute('filtroB','nover');
			}
		}
	}
	
	
	
	
	function filtrarActs(_hatch){
		//TODO si encuentra un listado de actividades que muestre deacuerdo al criterio de filtro.
	}
	
	function BuscarExternos(_hatch){
		//TODO si encuentra un mapa, realiza una consulta con búscqueda de puntos de otras actividades.
	}
	
	document.querySelector('[name="busqueda"]').focus();
	
</script>

	
	<?php
}
?>


