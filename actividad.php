<?php 
/**
* actividad.php
*
* aplicación principal de intefaz, formulario de visualización y carga de nuevos puntos de relevamiento

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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);


ini_set('display_errors',true);



// verificación de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){
	$e=explode('/',__FILE__);
	$f=$e[(count($e)-1)];
	$dest="DEST=$f";
	foreach($_GET as $kg => $Vg){
		$dest.='&'.$kg.'='.$Vg;		
	}
	header('Location: ./login.php?'.$dest);
}

// función de consulta de actividades a la base de datos 
include("./actividades_consulta.php");

$ID = isset($_GET['actividad'])?$_GET['actividad'] : '';
$RID = isset($_GET['registro'])?$_GET['registro'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

// define si se esta defininedo una argumentación, de no ser así esta aplicación solo devuelve un mensaje escrito
if(isset($_POST['actividad'])){
	$Actividad=$_POST['actividad'];
	$ACCION = $_POST['accion'];
}elseif(isset($_GET['actividad'])){
	$Actividad=$_GET['actividad'];
}else{
	$Actividad='';
}

if($Actividad==''){
	//header('location: ./actividades.php');	//si no hay una actividad definida esta página no debería consultarse
	echo "ERROR de Acceso 1";
	//break;
}

$UsuariosList = usuariosconsulta($ID);

// filtro de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 
$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	

// filtro temporal de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 	
$fechadesde_a=isset($_GET['fechadesde_a'])?str_pad($_GET['fechadesde_a'], 4, "0", STR_PAD_LEFT):'0000';
$fechadesde_m=isset($_GET['fechadesde_m'])?str_pad($_GET['fechadesde_m'], 2, "0", STR_PAD_LEFT):'00';
$fechadesde_d=isset($_GET['fechadesde_d'])?str_pad($_GET['fechadesde_d'], 2, "0", STR_PAD_LEFT):'00';
if($fechadesde_a!='0000'&&$fechadesde_m!='00'&&$fechadesde_d!='00'){
	$FILTROFECHAD=$fechadesde_a."-".$fechadesde_m."-".$fechadesde_d;
}else{
	$FILTROFECHAD='';
}
$fechahasta_a=isset($_GET['fechahasta_a'])?str_pad($_GET['fechahasta_a'], 4, "0", STR_PAD_LEFT):'0000';
$fechahasta_m=isset($_GET['fechahasta_m'])?str_pad($_GET['fechahasta_m'], 2, "0", STR_PAD_LEFT):'00';
$fechahasta_d=isset($_GET['fechahasta_d'])?str_pad($_GET['fechahasta_d'], 2, "0", STR_PAD_LEFT):'00';
if($fechahasta_a!='0000'&&$fechahasta_m!='00'&&$fechahasta_d!='00'){
	$FILTROFECHAH=$fechahasta_a."-".$fechahasta_m."-".$fechahasta_d;
}else{	
	$FILTROFECHAH='';
}

$seleccion['zoom'] = 0;

// función para obtener listado formateado html de actividades
$res= actividadesconsulta($ID,$seleccion);
$Contenido =  reset($res);
//echo "<pre>";print_r($Contenido);echo "</pre>";

	if(!isset($Contenido['Acc'][2])){$Contenido['Acc'][2]=array();}
	foreach($Contenido['Acc'][2] as $acc => $accdata){
		if($accdata['id_usuarios']==$UsuarioI){
			$Coordinacion='activa';
		}
	}
if(!isset($Contenido['Acc'][3])){$Contenido['Acc'][3]=array();}
	foreach($Contenido['Acc'][3] as $acc => $accdata){
		if($accdata['id_usuarios']==$UsuarioI){
			$Administracion='activa';
			$Coordinacion='activa';
		}
	}

$res=actividadesconsulta($ID,$seleccion);
$Actividad=reset($res);
if($Actividad['zz_PUBLICO']!='1'&&$Actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){
	echo "<h2>Error en el acceso, esta actividad no se encuentra aún publicada y usted no se encuentra registrado como autor de la misma.</h2>";
	exit();
}

if($RID>0){

}

?>
<!DOCTYPE html>
<head>
	<title>UNmapa - Área de Trabajo</title>
	<?php include("./includes/meta.php");?>
	<link rel="icon" href="./img/unmicon.ico">
	
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
	<link href="css/UNmapa.css" rel="stylesheet" type="text/css">
	<link href="css/actividad.css" rel="stylesheet" type="text/css">		
	
  <script src="./js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
  <script src="./js/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
  <link rel="stylesheet" href="./js/jquery-ui-1.11.4.custom/jquery-ui.css">

  <style>
  	#bloquIdent{
  		position:absolute;
  		top:0px;
  		left:2px;
  		font-size:50%;
  	}
	#bloquIdent p{
		font-size:120%;
		color:#55f;
	}
	#bloquIdent p span{
		font-size:140%;
	}
	#formulario[tipo='edicion'] #bloquIdent p{
		font-size:120%;
		color:#f55;
	}	
	
	#cargando{
		width:100%;
		height:100%;
		position:absolute;
		top:0;
		left:0;
		z-index:0;
	}
	
	#cargando #barra{
		position:absolute;
		background-color:pink;
		height:100%;
	}
	#cargando #avance{
		position:absolute;
		color:#444;
	}
	
	img#linkimagen:hover{
		border:#000;
		box-shadow:5px 5px 10px rgba(0,0,0,0.8);
	}
	
	#muestraimagen[estado="vacio"]{
		display:none;
		width:0px;
		height:0px;
	}
	
	#muestraimagen{
		overflow:hidden;
		position:fixed;
		left:10vw;
		top:10vh;
		width:80vw;
		height:80vh;
		border:3px solid #55f;
		background-color: #abf;
		box-shadow: 10px 10px 60px 100px rgba(255,255,255,0.9); 
		border-radius: 15px;
		transition: width 2s;
		z-index:1000;
	}
	
	
	
	#portaimagen{
		width: 90%;
		max-height: 100%;
		height: 100%;
		display: block;
		margin: auto;
		text-align: center;
	}

	#cerrarimagen:hover{
		background-color:#55f;
	}		
	#cerrarimagen{
		position:absolute;
		color:#000;
		background-color:#fff;
		border:1px solid #000;
		top:6px;
		right:6px;
		font-size:16px;
		z-index:2;
	}
	
	#portaimagen img{
		margin: auto;
		max-width: calc(100% - 3px);
		max-height: 90%;
		display: inline-block;
		vertical-align: middle;
		border: 1px solid;
	}
	#alineacionverticalmagen{
		height: 100%;
		vertical-align: middle;
		display: inline-block;
		margin: auto;
	}		
	
		
  </style>
  


</head>

<body>
	
	<!-- este proyecto recurre al proyecto tiny_mce para las funciones de edición de texto -->
	<script type="text/javascript" src="./js/tinymce43/tinymce.min.js"></script>
	
	
	<script type="text/javascript">
		var _IdReg = '<?php echo $RID;?>';
	</script>

	
	<script>	 
	  $(function() {
	    $( "#slider-vertical" ).slider({
	      orientation: "vertical",
	      min: 0,
	      max: 100,
	      value: 100,
	      slide: function( event, ui ) {
	      	     _listado= document.getElementById('puntosdeactividad');	

	     	_ch=_listado.clientHeight;
	     	_sh=_listado.scrollHeight-_ch;
	        $( "#puntosdeactividad" ).scrollTop( (_sh/100)*(100-ui.value) );
	        console.log((_sh/100)*(100-ui.value));
	      }
	    });
	    //$( "#puntosdeactividad" ).scrollTop( ($( "#puntosdeactividad" ).scrollHeight/100)*(100-slider( "value" )) );
	  });

  </script>
  
	<?php
	include('./includes/encabezado.php');
	?>
	
	<div class='recuadro' id="recuadro3" >		
		<div id="slider-vertical" style="height:200px;">
		</div>	
		<h4>Puntos relevados en esta actividad</h4>
		<a onclick="obtenerDescarga(this);">generar copia de descarga de la actividad</a>
		<a href="./actividad_reporte.php?actividad=<?php echo $ID;?>">ver resumen de contenidos de la actividad</a>
		<ul id='puntosdeactividad'>		
		</ul>
	</div>
	<div class='recuadro' id="recuadro2" >			
		<h4>Puntos visualizados del banco de datos</h4>
		<ul id='puntosdebase' class='scroll-content'></ul>	
	</div>
		
	<div id="pageborde"><div id="page">
		<h1>Actividad: <span class='resumen'><?php echo $Actividad['resumen'];?></span></h1>
		
		<?php

		if($Coordinacion=='activa'){
			echo "<a href='./actividad_config.php?actividad=$ID'>configurar esta actividad</a>";
		}
		
		
		echo " / <span class='menor'>web de acceso directo: <span class='resaltado'>".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?actividad=".$ID."</span></span>";	
		if($RID>0){$cons='verpuntos';}else{$cons='marcarpuntos';}	
		
		//echo"<pre>";print_r($Actividad);echo"</pre>";
		// formulario para agregar una nueva actividad		
		if($ID==''){
			echo "la actividad no fue llamada correctamnte";
			exit;
		}
			
			echo "<p>".nl2br($Actividad['consigna'])."</p>";				
				
			if($Actividad['hasta']<=$HOY&&$Actividad['hasta']>'0000-00-00'){
				$Stat='cerrada';	
			}
		

		?>
		<iframe name='mapa' id='mapa'></iframe>
		 	
	<input type='hidden' id='actividad' name='actividad' value='<?php echo $Actividad['id'];?>'>
	
	<div id='formulario' tipo='cerrado' class='formulario vista'>	
		<a class='cv' onclick='cierraVentana(this)'>- X</a><a class='av' onclick='abreVentana(this)'>- O</a>
		<div id='terminada'>
			<h2>Esta actividad ha cerrado el día <span id='hasta'></span>, para la carga de datos</h2>
			<h3>Resultados Obtenidos</h3><p id='resultados'></p>
			<h3>Objeto de estudio</h3><p id='objeto'></p>
			<h3>Marco de la actividad</h3><p id='marco'></p>
		</div>
		<div id='noinicio'>
			<h2>Esta actividad se abrirá el día <span id='desde'></span>, para la carga de datos</h2>
		</div>		
	</div>
					
	<div id='formulario' tipo='vista' class='formulario vista'>
		
		<div id='bloquIdent'>
			<p>autoría: <span id='autoria'></span></p>
			<p>punto: <span id='idtx'></span></p>
		</div>
		
		<div id='bloqudescricion'>
			<p>Retirado por: <span id='bAu'></span></p>
			<p>Mensaje: <span id='bTx'></span></p>
		</div>

		<form autocomplete='off' id='formCoord' enctype='multipart/form-data' method='post' action='./MAPAactividad.php' target='mapa'>
			<input type='hidden' id='actividad' name='actividad' value=''>
			<input type='hidden' id='rid' name='rid' value=''>
			<input id='inputAccion' type='hidden' name='accion' value=''>
			
			<input 
				id='bloqPu'
				modo='retir' 
				type='button' 
				value='Retirar Punto'  
				title='Al retirarse un punto solo puede ser visto por su autor y por el coordinador que lo ha retirado' 
				onclick='this.style.display="none";
					document.getElementById("bloq").style.display="block";
					document.getElementById("bloqPuNo").style.display="block";
					document.getElementById("bloqTx").style.display="block";
					document.getElementById("bloqTxL").style.display="block";'
			>
	
			<input 
				id='bloqPu' 
				modo='reinc'
				type='button' 
				value='Reincorporar Punto'  
				title='Al reincorporarse el punto volverá a ser visible por todos los participantes' 
				onclick='desbloquearPunto(this)'
			>


			<input 
				id='bloqPuNo' 
				style='display:none;' 
				type='button' 
				value='Cancelar'  
				title='cancelar eliminación' 
				id='bloqPuNo'
				onclick='this.style.display="none";
					document.getElementById("bloq").style.display="none";
					document.getElementById("bloqTx").style.display="none";
					document.getElementById("bloqTxL").style.display="none";
					document.getElementById("bloqPu").style.display="block";
			'>
			
			<span id='bloqTxL'>mensaje:</span>
			<span id='bloqTxVer' style='display:block'></span>
			<input id='bloqTx' name='bloqTx' type='text' value='- escriba mensaje -' onclick='validarContenido(this);'>
			<input id='bloq' type='button' onclick='bloquearPunto(this)' value='Confirmo'>
		</form>
		
		<label for='y'>Lat:</label><span type='text' name='y' id='y'></span>
		<label class='lon' for='x'>Lon:</label><span type='text' name='x' id='x'></span>
		<input type='hidden' name='z' id='z' value=''>
		<input type='hidden' id='actividad' name='actividad' value=''>	
					<input type='hidden' name='xPsMerc' id='xPsMerc' value=''>
			<input type='hidden' name='yPsMerc' id='yPsMerc' value=''>
			<input type='hidden' name='zResPsMerc' id='zResPsMerc' value=''>
		<br>
		
		<div id='campolink'>
			<label for='link'></label>
			<span id='link'></span>
			<a id='linkweb' title='' target='_blank' href=''>ver link</a>
			<a id='linkarchivo'  href='' download>/ descargar</a>	
			<img id='linkimagen' src='' onclick='mostrarImagen(this.getAttribute("src"))'>
		</div>
		
		<div id='campovalor'>
			<br><label for='valor'></label>
			<span name='valor' id='valor' value=''><div class='aclaracion'></div>
		</div>
		
		<div id='campocategoria'>
			<label for='categoria'></label>
			<span id='categoria'>
			</span>	
		</div>	
		
		<div id='campotextobreve'>
			<label for='textobreve'></label>
			<span name='valor' id='textobreve'></span>
		</div>
						
		<div id='campotexto'>
			<label for='texto'></label>
			<div id='texto'></div>
		</div>
	</div>

									
	<div class='formulario activo' id='formulario' tipo='edicion'>
		<div id='bloquIdent'>
			<p>autoría: <span id='autoria'></span></p>
			<p>punto: <span id='idtx'></span></p>
		</div>
		
		<div id='bloqudescricion'>
			<p>Retirado por: <span id='bAu'></span></p>
			<p>Mensaje: <span id='bTx'></span></p>
		</div>
				
		<form id='formPuntos' enctype='multipart/form-data' method='post' action='./MAPAactividad.php' target='mapa'>
			<input id='inputrid' type='hidden' name='rid' value=''>
			<input id='inputsubmit' type='button' value='Crear' onclick='enviarForm(this);'>
			<input id='inputnuevo' type='button' value='Reiniciar'  title='Al crear un nuevo punto perderá cualquier cambio introducido en el formulario' onclick='nuevoPunto(this);'>
			<input id='elim' type='button' value='Borrar Punto'  title='Al eliminarse un punto se perdera toda la información asociada al mismo'onclick='borrarPunto(this);'>
			<a class='botonamarcar marcar' onclick='location.reload();'>cargar nuevos puntos</a>
			<label for='y'>Lat :</label> <input readonly type='text' name='y' id='y' value=''>
			<label class='lon' for='x'>Lon :</label> <input readonly type='text' name='x' id='x' value=''>
			<input type='hidden' name='z' id='z' value=''>
			<input type='hidden' name='xPsMerc' id='xPsMerc' value=''>
			<input type='hidden' name='yPsMerc' id='yPsMerc' value=''>
			<input type='hidden' name='zResPsMerc' id='zResPsMerc' value=''>
			<br>
			
			<div id='campolink'>
				<label for='link'></label>
				<input id='link' name='link' value='' placeholder='copiar url aquí'>
				<br>
				<a style='margin-left:300px' id='linkweb' title='' target='_blank' href=''>ver link</a>
				<a id='linkarchivo'  href='' download>descargar</a>	
				<img id='linkimagen' src='' onclick='mostrarImagen(this.getAttribute("src"))'>
			</div>	
			
			<input id='inputAccion' type='hidden' name='accion' value=''>
			
			<div id='campovalor'>
				<br><label for='valor'></label>
				<input name='valor' id='valor' value=''><div class='aclaracion'></div>
			</div>	
			
			<div id='campotextobreve'>
				<label for='textobreve'></label>
				<input name='textobreve' id='textobreve' value=''>
			</div>		
			
			<div id='campocategoria'>
				<label for='categoria'></label>
				<select id='categoria' name='categoria' onchange='updateStylo(this)'>
					<option style='border:1px solid #550;' value=''>-elegir categoria-</option>
				</select>
				<input disabled='disabled' id='inputcatgorianueva' style='display:none' name='nuevacategoria' type='text' value='-escriba el nombre de la nueva categoría-' onclick='validarContenido(this);'>
				<input 
					id='campoCrearCategoria'
					type='button' 
					value='crear categoría' 
					onclick='
						this.parentNode.querySelector("#categoria").style.display="none";
						this.parentNode.querySelector("#inputcatgorianueva").style.display="inline-block";
						this.parentNode.querySelector("#inputcatgorianueva").removeAttribute("disabled");
						this.parentNode.querySelector("#categoria [value=\"\"]").selected = true;
						this.style.display="none";
					'
				>
			</div>
				
			<div id='campotexto'>
				<label for='texto'></label>
				<textarea name='texto' id='texto'></textarea>
			</div>
		</form>

		<form id='adjuntador' enctype='multipart/form-data' method='post' action='./agrega_adjunto.php' target='cargaimagen'>			
			<label style='position:relative;' class='upload'>
			<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo</span>		
			<div id='cargando'><div id='barra'></div><div id='avance'></div></div>					
			<input id='uploadinput' style='position:relative;opacity:0;' type='file' name='upload' value='' onchange='enviarAdjunto(this,event);'></label>
			<input type='hidden' id='actividad' name='actividad' value='<?php echo $Actividad['id'];?>'>
			
			
		</form>
		<iframe id='cargaimagen' name='cargaimagen'></iframe>
		
	</div>
	
	<div id='muestraimagen' estado='vacio'>
		<a id='cerrarimagen' onclick='this.parentNode.setAttribute("estado","vacio")'> X - cerrar </a>
		<div id='portaimagen'><div id='alineacionverticalmagen'></div></div>
	</div>
</div>
</div>



<script type="text/javascript">

	tinymce.init({ 
		selector:'textarea', 
		menubar: false,
		width : "540px",
		height : "120px",
		skin : "unmapa",
		});
</script>
	
<script type="text/javascript">
	var _StatusEnvio='listo';
	
	var _CargandoFormulario ='no';

	var _Uid='<?php echo $UsuarioI;?>';
	var _Aid='<?php echo $ID;?>';
	var _Adata={};
	var _Pdat=undefined;
	//var _Pdat=Array();
	var _mapainicialCargado='no';
	var _actividadConsultada='no';
	var _Aactiva='no';	
	
	
	
	function reiniciar(){		
		vaciarFormularioEdicion();	
		
		_Uid='<?php echo $UsuarioI;?>';
		_Aid='<?php echo $ID;?>';
		_Adat=Array();
		_Pdat=undefined;
		
		//var _Pdat=Array();
		_mapainicialCargado='no';
		_actividadConsultada='no';
		_CargandoFormulario ='no';
		_Aactiva='no';
		
		document.getElementById('puntosdeactividad').innerHTML='';
		consultaActividad();
	}

	function consultaActividad(){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}   
		_datos={};
		_datos["aid"]=_Aid;
	
		$.ajax({
			data: _datos,
			url:   'punto_consulta.php',
			type:  'post',
			success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					_Data=_res.data;					
					cargaFormulario(_res);
					cargaMapa();
			}
		})
	}
	
	consultaActividad();
	
	function cargaMapa(){
		document.getElementById('mapa').src='./MAPAactividad.php?actividad='+_Aid;
	}
	
	function vaciarFormularioEdicion(){
		
		_pf=document.querySelector('#formulario[tipo="edicion"]');		
		_in='value';		
		_pf.querySelector('#y')[_in]=null;
		_pf.querySelector('#x')[_in]=null;
		_pf.querySelector('#z')[_in]=null;		
		_pf.querySelector('#inputsubmit').value='Crear';
		_pf.querySelector('#inputrid').value='';
		_pf.querySelector('#valor')[_in]='';
		
		if(_ac.categLib=='1'){_pf.querySelector('#campoCrearCategoria').style.display='inline-block';
		}else{_pf.querySelector('#campoCrearCategoria').style.display='none';}
		
		
		_pf.querySelector('#categoria [value=""]').selected = true;
		_pf.querySelector('#categoria').removeAttribute('style')
		
		_pf.querySelector('#textobreve')[_in]='';
		
		_editor = tinymce.get('texto'); // use your own editor id here - equals the id of your textarea
		_editor.setContent('');
			
		_pf.querySelector('#bloqudescricion #bAu').innerHTML='';
		_pf.querySelector('#bloqudescricion #bTx').innerHTML='';
	
		_pf.querySelector('#bloquIdent #autoria').innerHTML='';
		_pf.querySelector('#bloquIdent #idtx').innerHTML='';		
	
		if(_ac.adjuntosAct=='1'){
			_pf.querySelector('#campolink').style.display='inline-block';
			_pf.querySelector('#adjuntador').style.display='inline-block';
		}else{
			_pf.querySelector('#campolink').style.display='none';
			_pf.querySelector('#adjuntador').style.display='none';
		}
		
		_pf.querySelector('#link').value='';	
		_pf.querySelector('#linkimagen').removeAttribute('src');
		_pf.querySelector('#linkweb').removeAttribute('href');
		_pf.querySelector('#linkarchivo').removeAttribute('href');
		
		_pf.querySelector('#inputnuevo').value='Reiniciar';	
	}
	
	function cargaFormulario(_res){
		
		console.log(_res);
		console.log(_res);
		console.log("formulario ya en carga:"+_CargandoFormulario);
		
		if(_res.data.nuevo!=undefined){
			
			//responde a un click en un lugar sin datos de una actividad activa.
			_ac=_Adata;
			_ac.editor='1';
			
			if(_CargandoFormulario=='si'){
				_resetear='no';
			}else{
				_resetear='si';
			}
			
		}else{
			
			_ac=_res.data.actividad;			
			_resetear='si';
		}
		
		if(_resetear=='si'&&_CargandoFormulario=='si'){
			vaciarFormularioEdicion();
		}
		
		console.log("resetar: "+_resetear);
		
		_CargandoFormulario='si';
		
		_Adata=_ac;
		
		_pf=document.querySelector('#formulario[tipo="cerrado"]');
		document.querySelector('#formulario[tipo="cerrado"]').style.display='block';
		document.querySelector('#formulario[tipo="vista"]').style.display='block';
		document.querySelector('#formulario[tipo="edicion"]').style.display='block';
		
		if(_ac.estado=='activa'){
			
			_Aactiva='si';
			_pf.style.display='none';
			
		}else{
			
			_CargandoFormulario = "no";
			
			_pf.style.display='inline-block';
			_pf.querySelector('#desde').innerHTML=_ac.desde;
			_pf.querySelector('#hasta').innerHTML=_ac.hasta;
			
			_pf.querySelector('#resultados').innerHTML=_ac.resultados;
			_pf.querySelector('#objeto').innerHTML=_ac.objeto;
			_pf.querySelector('#marco').innerHTML=_ac.marco;
			
			if(_ac.estado=='terminada'){
				_pf.querySelector('#terminada').style.display='block';
				_pf.querySelector('#noinicio').style.display='none';
			}else if(_ac.estado=='noinicio'){
				_pf.querySelector('#noinicio').style.display='block';
				_pf.querySelector('#terminada').style.display='none';
			}
		}
		//console.log(_ac.editor);
		
		if(_ac.editor=='1'){
						
			document.querySelector('#formulario[tipo="vista"]').style.display='none';
			_pf=document.querySelector('#formulario[tipo="edicion"]');
			
			if(_resetear!='no'){
				
				_pf.querySelector('#categoria').innerHTML="<option style='border:1px solid #550;' value=''>-elegir categoria-</option>";
				
				for(_no in  _ac.catOrden){
					_nc=_ac.catOrden[_no];
					_dc = _ac.categorias[_nc];
					_op=document.createElement('option');
					_op.value=_dc.id;
					_op.innerHTML=_dc.nombre;
					_op.title=_dc.descripcion;
					_op.style.backgroundColor=_dc.CO_color;					
					_col = _dc.CO_color.replace("rgb(", "");
					_col = _col.replace(")", "");
					_rgb =_col.split(',');	
					console.log(_rgb);	
					_val =(parseInt(_rgb[0])*0.299 + parseInt(_rgb[1])*0.587 + parseInt(_rgb[2])*0.114);
					console.log(_val);
					if( _val > 165){
						_fc ='#000000';
					}else{
						_fc = '#ffffff';
					}
					
					_op.style.color=_fc;
					_pf.querySelector('#categoria').appendChild(_op);				
				}
			}
			
			_pf.querySelector('#inputcatgorianueva').style.display='none';
			_pf.querySelector('#inputcatgorianueva').value='';
			if(_ac.categLib=='1'){_pf.querySelector('#campoCrearCategoria').style.display='inline-block';
			}else{_pf.querySelector('#campoCrearCategoria').style.display='none';}
				
		}else if(_ac.editor=='0'){						
			document.querySelector('#formulario[tipo="edicion"]').style.display='none';
			_pf=document.querySelector('#formulario[tipo="vista"]');	
			_CargandoFormulario = "no";		
		}
		
		_pf.querySelector('#bloqudescricion').style.display='none';
		
		if(_ac.valorAct=='1'){_pf.querySelector('#campovalor').style.display='inline-block';
		}else{_pf.querySelector('#campovalor').style.display='none';}
		
		if(_ac.textoAct=='1'){_pf.querySelector('#campotexto').style.display='inline-block';
		}else{_pf.querySelector('#campotexto').style.display='none';}

		if(_ac.textobreveAct=='1'){_pf.querySelector('#campotextobreve').style.display='block';
		}else{_pf.querySelector('#campotextobreve').style.display='none';}
		
		if(_ac.categAct=='1'){_pf.querySelector('#campocategoria').style.display='inline-block';
		}else{_pf.querySelector('#campocategoria').style.display='none';}
		
		
		if(_ac.adjuntosAct=='1'){
			_pf.querySelector('#campolink').style.display='inline-block';
			if(_ac.editor=='1'){
				_pf.querySelector('#adjuntador').style.display='inline-block';
			}
		}else{
			_pf.querySelector('#campolink').style.display='none';
			if(_ac.editor=='1'){
				_pf.querySelector('#adjuntador').style.display='none';
			}
		}
		
		
		
		_pf.querySelector('label[for="link"]').innerHTML=_ac.adjuntosDat+" :";
		_pf.querySelector('label[for="valor"]').innerHTML=_ac.valorDat+" :";
		_pf.querySelector('label[for="textobreve"]').innerHTML=_ac.textobreveDat+" :";
		_pf.querySelector('label[for="texto"]').innerHTML=_ac.textoDat+" :";
		_pf.querySelector('label[for="categoria"]').innerHTML=_ac.categDat+" :";
		
		
		if(_resetear!='no'){	
			if(_pf.querySelector('#linkimagen')!=null){_pf.querySelector('#linkimagen').style.display='none';}
			if(_pf.querySelector('#linkweb')!=null){_pf.querySelector('#linkweb').style.display='none';}
			if(_pf.querySelector('#linkarchivo')!=null){_pf.querySelector('#linkarchivo').style.display='none';}
		}
		
		if(_ac.editor=='0'){
			_in='innerHTML';
		}else if(_ac.editor=='1'){				
			_in='value';
		}

		if(_res.data.punto==undefined){

			_pf.querySelector('#y')[_in]='';
			_pf.querySelector('#x')[_in]='';
			_pf.querySelector('#z')[_in]='';
			
			_pf.querySelector('#yPsMerc')[_in]='';
			_pf.querySelector('#xPsMerc')[_in]='';
			_pf.querySelector('#zResPsMerc')[_in]='';
			

			if(_res.data.nuevo!=undefined){
				//console.log(_res.data.nuevo);
				_Pdat=_res.data.nuevo;
				_pf.querySelector('#y')[_in]=_res.data.nuevo.x;
				_pf.querySelector('#x')[_in]=_res.data.nuevo.y;
				_pf.querySelector('#z')[_in]=_res.data.nuevo.z;
				
				_pf.querySelector('#yPsMerc')[_in]=_res.data.nuevo.yPsMerc;
				_pf.querySelector('#xPsMerc')[_in]=_res.data.nuevo.xPsMerc;
				_pf.querySelector('#zResPsMerc')[_in]=_res.data.nuevo.zResPsMerc;
			}

			if(_resetear!='no'){				
				_pf.querySelector('#textobreve')[_in]='';
				_pf.querySelector('#valor')[_in]='';
				_pf.querySelector('#link')[_in]='';
			}
			
			if(_ac.editor=='0'){	
				_pf.querySelector('#categoria').innerHTML='';
				_pf.querySelector('#texto').innerHTML='';				
			}else{
				if(_resetear!='no'){			
					_pf.querySelector('[value=""]').selected = true;
				}
			}
			_pf.querySelector('#categoria').removeAttribute('style')

		}else{	

			_pf.querySelector('#y')[_in]=_res.data.punto.y;
			_pf.querySelector('#x')[_in]=_res.data.punto.x;
			_pf.querySelector('#z')[_in]=_res.data.punto.z;

			_pf.querySelector('#yPsMerc')[_in]=_res.data.punto.yPsMerc;
			_pf.querySelector('#xPsMerc')[_in]=_res.data.punto.xPsMerc;
			_pf.querySelector('#zResPsMerc')[_in]=_res.data.punto.zResPsMerc;

			_pf.querySelector('#textobreve')[_in]=_res.data.punto.textobreve;
			_pf.querySelector('#valor')[_in]=_res.data.punto.valor;
			//_pf.querySelector('#texto')[_in]=_res.data.punto.texto;

			if(_ac.editor=='0'){	
				_pf.querySelector('#categoria').innerHTML=_res.data.punto.categoriaNom;
				_pf.querySelector('#texto').innerHTML=_res.data.punto.texto;				
			}else{
				_pf.querySelector('#link')[_in]=_res.data.punto.link;
				_editor = tinymce.get('texto'); // use your own editor id here - equals the id of your textarea
				_editor.setContent(_res.data.punto.texto);
				_pf.querySelector('#inputnuevo').value='Nuevo Punto';
				if(_resetear!='no'){
					//_pf.querySelector('#categoria option[value=""]').innerHTML=_res.data.punto.categoriaNom;	
					if(_res.data.punto.categoria!=null){					
						_pf.querySelector('[value="' + _res.data.punto.categoria + '"]').selected = true;
						//_res.data.punto.categoria.style=_pf.querySelector('[value="' + _res.data.punto.categoria + '"]').style;
						
					}
					_pf.querySelector('#inputsubmit').value='Guardar';
					_pf.querySelector('#inputrid').value=_res.data.punto.id;
				}				
			}
			
			if(_ac.editor=='1'||_ac.docente=='1'){		
				if(_res.data.punto.zz_bloqueado=='1'){
					_pf.querySelector('#bloqudescricion').style.display='block';
					_pf.querySelector('#bloqudescricion #bAu').innerHTML=_res.data.punto.zz_bloqueadoN+" "+_res.data.punto.zz_bloqueadoA;
					_pf.querySelector('#bloqudescricion #bTx').innerHTML=_res.data.punto.zz_bloqueadoTx;
					_pf.querySelector('#bloqPu[modo="reinc"]').style.display='none';
					
				}else{
					_pf.querySelector('#bloqudescricion').style.display='none';
					if(_pf.querySelector('#bloqPu[modo="retir"]')!=null){
						_pf.querySelector('#bloqPu[modo="retir"]').style.display='none';
					}
				}
			}
			
			_pf.querySelector('#bloquIdent #autoria').innerHTML=_res.data.punto.nombre +" "+ _res.data.punto.apellido;
			_pf.querySelector('#bloquIdent #idtx').innerHTML=_res.data.punto.id;		
			
			
			if(_ac.docente=='1'){	
				document.getElementById("bloqPuNo").style.display="none";
				document.getElementById("bloq").style.display="none";
				document.getElementById("bloqTx").style.display="none";
				document.getElementById("bloqTxL").style.display="none";
				document.getElementById("bloqPu").style.display="block";
				
				
				if(_ac.editor=='0'){
					_pf.querySelector('#formCoord').style.display='block';
					
					if(_res.data.punto.zz_bloqueado=='1'){
						document.querySelector('#bloqPu[modo="reinc"]').style.display='block';
						document.querySelector('#bloqPu[modo="retir"]').style.display='none';
						document.querySelector('#formCoord #inputAccion').value='bloquear';					
					}else if(_res.data.punto.zz_bloqueado=='0'){
						document.querySelector('#bloqPu[modo="reinc"]').style.display='none';
						document.querySelector('#bloqPu[modo="retir"]').style.display='block';
						document.querySelector('#formCoord #inputAccion').value='desbloquear';
					}
				}
			}else{
				document.querySelector('#bloqPu[modo="retir"]').style.display='none';
				document.querySelector('#bloqPu[modo="reinc"]').style.display='none';				
			}
			
			
			
			//console.log('acceso:');
			//console.log(_ac);
			//console.log(_res.data.punto);
			_pf.querySelector('#categoria').style.backgroundColor=_res.data.punto.categoriaCol;
			_col = _res.data.punto.categoriaCol.replace("rgb(", "");
			_col = _col.replace(")", "");
			_rgb =_col.split(',');	
			//console.log(_rgb);	
			_val =(parseInt(_rgb[0])*0.299 + parseInt(_rgb[1])*0.587 + parseInt(_rgb[2])*0.114);
			//console.log(_val);
			if( _val > 165){
				_fc ='#000000';
			}else{
				_fc = '#ffffff';
			}
			
			
			
			
			
			_pf.querySelector('#categoria').style.color=_fc;
			_pf.querySelector('#categoria').style.borderColor=_res.data.punto.categoriaCol;
			_pf.querySelector('#categoria').style.borderStyle='solid';
			_pf.querySelector('#categoria').style.borderWidth='1px';
			
			
			if(_resetear!='no'){
				
				
				if(_res.data.punto.linkTipo=='weblink'){
					
					
					if(_res.data.punto.linkForm=='imagen'){
						_pf.querySelector('#linkimagen').src=_res.data.punto.link;
						_pf.querySelector('#linkimagen').style.display='block';
						
						_pf.querySelector('#linkweb').setAttribute('href',_res.data.punto.link);
						_pf.querySelector('#linkweb').style.display='inline-block';				
												
					}else{
						_pf.querySelector('#linkarchivo').setAttribute('href',_res.data.punto.link);
						_pf.querySelector('#linkarchivo').setAttribute('download','');
						_pf.querySelector('#linkarchivo').style.display='inline-block';
					}
					
					
				}else{
					
					if(_res.data.punto.linkForm=='imagen'){
						
						_pf.querySelector('#linkimagen').src=_res.data.punto.link;
						_pf.querySelector('#linkimagen').style.display='block';
						
						_pf.querySelector('#linkweb').setAttribute('href',_res.data.punto.link);
						_pf.querySelector('#linkweb').style.display='inline-block';				
						
					}else if(_res.data.punto.linkForm=='archivo'){
						_pf.querySelector('#linkarchivo').setAttribute('href',_res.data.punto.link);
						_pf.querySelector('#linkarchivo').setAttribute('download','');
						_pf.querySelector('#linkarchivo').style.display='inline-block';
					}
				}

			}
		}
		
		_actividadConsultada='si';
		
		if(_mapainicialCargado=='si'){
			document.getElementById("mapa").contentWindow.mostrarArea(_ac);
		}	
	}
	
	var _nFile=0;
		
	var xhr=Array();
	var inter=Array();

	function enviarAdjunto(_this,_event){
		
		var files = _this.files;
				
		for (i = 0; i < files.length; i++) {
	    	_nFile++;
	    	console.log(files[i]);
			var parametros = new FormData();
			parametros.append('upload',files[i]);
			parametros.append('nfile',_nFile);
			parametros.append('actividad',_Aid);
			
			var _nombre=files[i].name;
			//_upF=document.createElement('a');
			//_upF.setAttribute('nf',_nFile);
			//_upF.setAttribute('class',"archivo");
			//_upF.setAttribute('size',Math.round(files[i].size/1000));
			//_upF.innerHTML=files[i].name;
			//document.querySelector('#listadosubiendo').appendChild(_upF);
			
			_nn=_nFile;
			xhr[_nn] = new XMLHttpRequest();
			xhr[_nn].open('POST', './agrega_adjunto.php', true);
			//xhr[_nn].upload.li=_upF;
			
			
			xhr[_nn].upload.addEventListener("progress", updateProgress, false);			
			
			document.querySelector('#cargando').style.display='block';
			
			xhr[_nn].onreadystatechange = function(evt){
				//console.log(evt);
				document.querySelector('#cargando').style.display='none';
				document.querySelector('#cargando #barra').style.width=0;
				document.querySelector('#cargando #avance').innerHTML="";		
		
				if(evt.explicitOriginalTarget != undefined){
					
					if(evt.explicitOriginalTarget.readyState==4){
						var _res = $.parseJSON(evt.explicitOriginalTarget.response);
						//console.log(_res);
						for(_nm in _res.mg){alert(_res.mg[_nm]);}
						if(_res.res=='exito'){							
							
							document.querySelector('#formulario[tipo="edicion"] #link').value=_res.data.nuevonombre;
							
							if(_res.data.tipo=='imagen'){
								//console.log('hola');
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').setAttribute('src',_res.data.nuevonombre);
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').style.display='block';
								document.querySelector('#formulario[tipo="edicion"] #linkarchivo').style.display='none';
								document.querySelector('#formulario[tipo="edicion"] #linkweb').style.display='none';	
							}else{
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').setAttribute('src','');
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').style.display='none';
							}
							
						}else{
							document.querySelector('#formulario[tipo="edicion"] #link').value='error';
						}

					}
				}else{
					
                    if(evt.currentTarget.readyState==4){
                        var _res = $.parseJSON(evt.target.response);
                        //console.log(_res);
                        for(_nm in _res.mg){alert(_res.mg[_nm]);}
                        if(_res.res=='exito'){
                        
                        	document.querySelector('#formulario[tipo="edicion"] #link').value=_res.data.nuevonombre;
							if(_res.data.tipo=='imagen'){
								//console.log('hola');
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').setAttribute('src',_res.data.nuevonombre);
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').style.display='block';
								document.querySelector('#formulario[tipo="edicion"] #linkarchivo').style.display='none';
								document.querySelector('#formulario[tipo="edicion"] #linkweb').style.display='none';	
							}else{
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').setAttribute('src','');
								document.querySelector('#formulario[tipo="edicion"] #linkimagen').style.display='none';
							}
							 
                        }else{
                            document.querySelector('#formulario[tipo="edicion"] #link').value='error';
                        }
                    }
										
				}
				
			}
			xhr[_nn].send(parametros);
		}			
	}
	
	function updateProgress(evt) {
	  if (evt.lengthComputable) {
	    var percentComplete = 100 * evt.loaded / evt.total;		
		document.querySelector('#cargando #barra').style.width=Math.round(percentComplete)+"%";
		document.querySelector('#cargando #avance').innerHTML="("+Math.round(percentComplete)+"%)";		
	  } else {
	    // Unable to compute progress information since the total size is unknown
	  }
	  
	}

	function obtenerDescarga(_this){
		var parametros = {
			"idactividad" : '<?php echo $ID;?>'
		};		
			
		$.ajax({
			data:  parametros,
			url:   'actividades_descarga_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				//console.log(_res);
				obtenerBotonDescarga(response, _this);
			}
		});
	}
	
	function obtenerBotonDescarga(response, _this){
		var _res = $.parseJSON(response);
		console.log(_res);
		_this.innerHTML='Descargar AHORA';
		_this.setAttribute('href',_res.data.url);
		//_this.setAttribute('onclick','removerDescarga(this);');
		_this.setAttribute('download','');
	}	

	
</script>

<script type='text/javascript'>
	// funciones UI para mostrar y ocultar información	

	function abreVentana(_this){
		_this.parentNode.setAttribute("ventana","abierta");
		_this.style.display="none";
		_this.previousSibling.style.display="block";
	}
	function cierraVentana(_this){
		_this.parentNode.setAttribute("ventana","cerrada");
		_this.style.display="none";
		_this.nextSibling.style.display="block";
	}
	
	function updateStylo(_this){
		_this.style.backgroundColor = _this.options[_this.selectedIndex].style.backgroundColor;
		_this.style.color = _this.options[_this.selectedIndex].style.color;
	}
	
	
	function enviarForm(_this){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}
		
		_parametros = {};		
		_inns=_this.parentNode.querySelectorAll('input, textarea, select');
					
		for(_nn in _inns){
			if(typeof _inns[_nn] == 'object'){
				_nom=_inns[_nn].getAttribute('name');
				_val=_inns[_nn].value;
				if(_nom==null){continue;}
				_parametros[_nom]=_val;
			}
		}
		_editor = tinymce.get('texto'); // use your own editor id here - equals the id of your textarea
		_parametros.texto=_editor.getContent();
		
		_parametros.aid=_Aid;
		_parametros.uid=_Uid;
		
		if(_parametros.x===''){
			alert('Falta indicar el lugar en al mapa!');
			return;
		}
		if(_parametros.y===''){
			alert('Falta indicar el lugar en al mapa!');
			return;
		}
		
		
		console.log(_parametros);
		
		if(_parametros.rid!=''){
			_StatusEnvio='formulario enviado';
			$.ajax({
				data:  _parametros,
				url:   'punto_guardar.php',
				type:  'post',
				success:  function (response){
					_StatusEnvio='listo';
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){
						reiniciar();
					}else{
						alert('error, vuelva a intentarlo');						
					}
				}
			});
		}else if(_parametros.rid==''){
			_StatusEnvio='formulario enviado';
			$.ajax({
				data:  _parametros,
				url:   'punto_crear.php',
				type:  'post',
				success:  function (response){
					_StatusEnvio='listo';
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){
						reiniciar();
					}else{
						alert('error, vuelva a intentarlo');						
					}
				}
			});			
		}
	}
	
	function borrarPunto(_this){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}
		if(confirm('¿Querés borrar el punto? (no se pùede deshacer)')){
			
			_parametros = {};	
			_parametros.rid=_this.parentNode.querySelector('#inputrid').value;
			_parametros.aid=_Aid;
			_parametros.uid=_Uid;
			
			console.log(_parametros);
			_StatusEnvio='formulario enviado';
			$.ajax({
				data:  _parametros,
				url:   'punto_borrar.php',
				type:  'post',
				success:  function (response){
					_StatusEnvio='listo';
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){
						reiniciar();
					}else{
						alert('error, vuelva a intentarlo');						
					}
				}
			});
		}
	}		

	
	function desbloquearPunto(_this){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}
		if(confirm('¿Querés reincorporar este punto al conjunto visible? (se perderán los datos del bloqueo actual)')){
			
			_parametros = {};	
			_parametros.rid=_Pdat.id;
			_parametros.aid=_Aid;
			_parametros.uid=_Uid;
			
			console.log(_parametros);
			_StatusEnvio='formulario enviado';
			$.ajax({
				data:  _parametros,
				url:   'punto_desbloquear.php',
				type:  'post',
				success:  function (response){
					_StatusEnvio='listo';
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){
						reiniciar();
					}else{
						alert(_res.mg[_nm]);			
					}
				}
			});
		}
	}		
			
	function bloquearPunto(_this){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}
		_bTx=_this.parentNode.querySelector('#bloqTx').value;
		if(_bTx==''){
			alert('cualquier bloqueo debe ser acompañado de un mensaje justificatorio');
			return;
		}
		
		if(confirm('¿Querés reitar este punto de la vista pública? (solo será visible por los docentes de esta actividad y por el autor. En todos los casos será acompañado del mensaje: '+_bTx+')')){
			
			_parametros = {};	
			_parametros.rid=_Pdat.id;
			_parametros.aid=_Aid;
			_parametros.uid=_Uid;
			_parametros.zz_bloqueadoTx=_bTx;
			
			console.log(_parametros);
			_StatusEnvio='formulario enviado';
			$.ajax({
				data:  _parametros,
				url:   'punto_bloquear.php',
				type:  'post',
				success:  function (response){
					_StatusEnvio='listo';
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){
						reiniciar();
					}else{
							alert('error, vuelva a intentarlo');			
					}
				}
			});
		}
	}	
					
	function nuevoPunto(){
		
		if(confirm('¿Querés crear un nuevo punto? (vaciarás el conteido del tu formulario)')){
			_Pdat=undefined;
			vaciarFormularioEdicion();			
			document.getElementById("mapa").contentWindow.reiniciarMapa();
		}	
	}
	
	
	function mostrarImagen(_src){
		
		$.ajax({
		    url:_src,
		    type:'HEAD',
		    error: function()
		    {
		        //file not exists
		    },
		    success: function()
		    {
		    	document.querySelector('#muestraimagen').setAttribute('estado','cargado');
		    	_porta=document.querySelector('#portaimagen');
		    	_porta.innerHTML='<div id="alineacionverticalmagen"></div>';
		    	_img=document.createElement('img');
		    	_img.setAttribute('src',_src);
		    	_porta.appendChild(_img);
		        //file exists
		    }
		});
		
	}
		
</script>
	
<script type='text/javascript'>

		function validarContenido(_this){
			
			if(_this.value=='-agregar nuevo-'){
				_this.setAttribute('value','');
			}
			if(_this.value=='-escriba el nombre de la nueva categoría-'){
				_this.setAttribute('value','');
			}
			if(_this.value=='- escriba mensaje -'){
				_this.setAttribute('value','');
			}
			
		}
	
</script>





<?php

include('./_serverconfig/pie.php');
?>
</body>

