<?php 
/**
* actividad_reporte.php
*
* aplicación para visualizar registros en forma de reporte no interactivo
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
	header('location: ./actividades.php');	//si no hay una actividad definida esta página no debería consultarse
	echo "ERROR de Acceso 1";
	exit;
}


$UsuariosList = usuariosconsulta($ID);

// medicion de rendimiento lamp 
$starttime = microtime(true);

$FILTROFECHAH='';

$seleccion['zoom'] = 0;

// función para obtener listado formateado html de actividades 
$Contenido =  reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Contenido);echo "</pre>";


foreach($Contenido['Acc'][2] as $acc => $accdata){
	if($accdata['id_usuarios']==$UsuarioI){
		$Coordinacion='activa';
	}
}
foreach($Contenido['Acc'][3] as $acc => $accdata){
	if($accdata['id_usuarios']==$UsuarioI){
		$Administracion='activa';
		$Coordinacion='activa';
	}
}

$Actividad=reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Actividad);echo "</pre>";
if($Actividad['zz_PUBLICO']!='1'&&$Actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){
	echo "<h2>Error en el acceso, esta actividad no se encuentra aún publicada y usted no se encuentra registrado como autor de la misma.</h2>";
	exit;
}


if($RID>0){
	$Registro=$Actividad['GEO'][$RID];
	//print_r($Registro);
	if($Registro['id_usuarios']==$UsuarioI){
		$Accion='cambia';
		$Valores=$Registro;
		$ValoresRef=$Registro;
		$AccionTx='Guardar cambios';
	}else{
		$Accion='ver';
		$ValoresRef=$Registro;
		$AccionTx='';			
	}
}else{
	$Accion='crear';
	$AccionTx='Guardar punto';
}
?>

<title>UNmapa - Área de Trabajo</title>
<?php include("./includes/meta.php");?>
<link href="css/treccppu.css" rel="stylesheet" type="text/css">
<link href="css/UNmapa.css" rel="stylesheet" type="text/css">	
	
	
  <link rel="stylesheet" href="./js/jquery-ui-1.11.4.custom/jquery-ui.css">
  <script src="./js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
  <script src="./js/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
  <link rel="stylesheet" href="./js/jquery-ui-1.11.4.custom/jquery-ui.css">
  <style>
.ui-slider .ui-slider-handle {
    width: 0.9em;
}

span.ui-slider-handle:hover {
    background-color:#007fff;
    border-color:#003eff;
}


.ui-slider-vertical .ui-slider-handle {
    left: -0.4em;
}
.ui-slider-vertical {
    width: 0.4em;
}
.ui-widget {
    font-size: 0.6em;
}
  </style>
  <script>
 
  $(function() {
    $( "#slider-vertical" ).slider({
      orientation: "vertical",
      min: 0,
      max: 100,
      value: 100,
      slide: function( event, ui ) {
      	     _listado= document.getElementById('puntosdeactividad');	
      /*_porc = 100 * _listado.scrollHeight / (_listado.scrollHeight-_listado.clientHeight); 
      
      px= ;*/
     	_ch=_listado.clientHeight;
     	_sh=_listado.scrollHeight-_ch;
        $( "#puntosdeactividad" ).scrollTop( (_sh/100)*(100-ui.value) );
        console.log((_sh/100)*(100-ui.value));
      }
    });
    //$( "#puntosdeactividad" ).scrollTop( ($( "#puntosdeactividad" ).scrollHeight/100)*(100-slider( "value" )) );
  });

	

  </script>
  	
	<style type='text/css'>
	a{
		cursor:pointer;
	}
	.dato.fecha{
	    width: 60px;
	}
	.dato.autor{
	    width: 90px;
	}		
	.dato.descripcion{
	    width: 180px;
	}		
	
	.dato.localizaciones, .dato.imagenes{
		font-size: 11px;
	}
	
	.elemento {
	    background-color: #ADD8E6;
	    border: 2px solid #08AFD9;
	    cursor: pointer;
	    display: inline-block;
	    font-size: 10px;
	    height: 14px;
	    overflow: hidden;
	    padding: 2px 1px;
	    position: relative;
	    width: 16px;
	 }
	 
	
	li{
		border:2px solid transparent;
	}
	
	li:hover div.punto{
		border-color:blue;
	}
	
	
	label, .aclaracion{			
	    display: inline-block;
	    font-weight: normal;
	    margin-right: 2px;
	    text-align: right;
	    vertical-align: middle;
	    width: 200px;
	}
	
	label.lon{
		width:50px;
	}
	
	label{
		 height: 26px;
	}
	.aclaracion{	
	 	text-align: left;
	 	margin:2px;
	 	border:none;		
	}
	
	.formulario{
		border:2px solid #f55;
		background-color:#ffb;
		margin: 10px;
		display:inline-block;
		position:relative;
		width:750px;
	}
	.formulario form{
		margin: 0px;
	}
			
	.formulario input,.formulario select{
		color: #f55;
		background-color:#ffb;
		vertical-align: middle;
		margin-top:2px;
		margin-bottom:2px;
	}	
	.formulario label,.formulario .aclaracion{
		color: #f55;
	}	
	
	.formulario.vista{
		border-color:#55f;
	}
	.formulario.vista, .formulario.vista input,.formulario.vista select,.formulario.vista label{
		color: #000;
		background-color:#abf;
	}			

	.vista #adjunto{
		border-color:#55f;
	}
			
	.formulario > span{
		min-height:26px;
		vertical-align: middle;
		display:inline-block;
		color:#55f;
	}
	
	form#adjuntador{
	    left: 442px;
	    position: absolute;
	    top: 27px;
	}		
		
	input#link{
		width: 236px;
	}		
			
	#inputsubmit{
		position:absolute;
		top:0px;
		right:0px;
		width:100px;
	}	
	#elimina,#elim,#elimNo{
		position:absolute;
		top:22px;
		right:0px;
		width:100px;
	}		
	#elimina{
		position:absolute;
		display:none;
		top:42px;
		background-color:red;
		color:#fff;
	}			
	
	#texto_parent{
		display: inline-block;
		vertical-align: middle;
	}
	div#texto_parent{
	    border: 1px solid #f55;
	    font-size: 12px;
	    height: 100px;
	    overflow-y: auto;
	    padding: 5px;
	    width: 530px;
	}
	
	.formulario.vista div#texto_parent {
		border-color:#55f;
		color:#55f;
	}
	#texto_parent>p{
		font-size: 12px;
		text-align:justify;
	}
	
	#tinymce{
		background-color:#ffb;
	}
	.resumen{
		font-size:15px;
		font-weight:normal;
	}
	
	#uploadinput{
		width:90px;
	}
	
	#inputcatgorianueva{
		width:236px;
	}
	
	a.botonamarcar{
		position:absolute;
		top:-22px;
		right:-2px;
		border-width:2px;
		border-style:solid;
	}	
	#slider { margin: 10px; }	

	#formCoord{
		
	}
	
	#formCoord input{
		right:4px;
		border: 2px solid red;
		position:absolute;
		background-color: #ffb;
    	color: #f55;
    	font-size:12px;
    	height: 15px;
    	line-height: 9px;
    	width:103px;
    	padding: 0;
	} 
	
	
	#bloqPu{
		top:2px;
	}
	#bloqPuNo{
		top:2px;
		display:none;
	}
	input#bloqTx{
		top:21px;
		display:none;
		border-color: #008 #88f #88f #008;
	    border-width: 1px;	    
	}
	
	#bloqTxL{
		top:25px;
		right:108px;
	    display: none;
	    position:absolute;
	    font-size:12px;
	    color: #f55;
	}	
	#bloqTxVer{
		top:25px;
		right:4px;
		width:200px;
	    display: none;
	    position:absolute;
	    font-size:12px;
	    color: #f55;
	    text-align:right;
	}		
	#bloq{
		top:40px;
		display:none;
	}
	#bloqudescricion{
		z-index:10;
	    background-color: #fcc;
	    border: 1px solid red;
	    font-size: 12px;
	    padding: 2px;
	    position: absolute;
	    right: 0;
	    top: 67px;
	    width: 200px;
	}
	#bloqudescricion>p{
		font-size:inherit;
	}
	#adjunto{
		left: 540px;
	    position: absolute;
	    top: 1px;
	}
	div.punto{
		display:inline-block;
		width:10px;
		height:10px;
		border-radius:5px;
		border-width:2px;
		border-style:solid;
		margin:10px;
	}
	div.registro{
		border-top:3px solid #000;
		margin:8px;
		margin-top:28px;
	}
	div.registro h2{
		margin:8px;
		font-size:15px;
	}
	div.registro img{
		max-height:200px;
		max-width:360px;
	}
	
	div.registro .c1, div.registro .c2{
		display:inline-block;
		width:360px;
		vertical-align:top;
	}
	
	div.registro .c2{
		border-left:1px solid #000;
	}
	
	#modelo{
		display:none;
	}
	
	.paquete[estado='0']{
		display:none;
	}	
	
	.registro[filtrob='nover']{
		display:none;
	}
		
	@media print {	
			
	   div.registro{page-exit-inside: avoid;}
	   body{
	   	background-color:#fff;
	   	background-image:none;
	   }
	   div#recuadro1{
	   	display:none;
	   }
	}
			
	</style>

</head>

<body>
	
	<?php
	include('./includes/encabezado.php');
	?>	
	<div id="pageborde"><div id="page">
		<h1>Actividad: <span class='resumen'><?php echo $Actividad['resumen'];?></span></h1>

		 / <span class='menor'>web de acceso directo: <span class='resaltado'>http://190.111.246.33/UNmapa/actividad.php?actividad=<?php echo $ID;?></span></span>
		
		<p id='consigna'></p>
		<p id='marco'></p>
		<p id='objeto'></p>
				
		<?php 
		
		if($RID>0){$cons='verpuntos';}else{$cons='marcarpuntos';}	
		
		//echo"<pre>";print_r($Actividad);echo"</pre>";
		// formulario para agregar una nueva actividad		
		if($ID==''){
				echo "la actividad no fue llamada correctamnte";
		}else{
			
			
		}
		?>
	
	</div></div>

<?php
include('./_serverconfig/pie.php');
?>

<div id='modelo' class="registro">
	<div class="c1">
		<h1>id: <span id='rid'></span></h1>
		<h2>por: <span id='autor'></span> | <span id='fecha'></span></h2>
		
		
		<div id='paqueteCategoria' class='paquete'>
			<h2 id="categDat"></h2>
			<div class="punto" style=""></div>
			<span id='categoriatx'></span><br>
		</div>
		<div id='paqueteTextobreve' class='paquete'>
			<h2 id='textobreveDat'></h2>
			<p id='textobreve'></p>
		</div>
		<div id='paqueteValor' class='paquete'>
			<h2 id='valorDat'></h2>
			<p><span id='valor'></span><span id='valorUni'></span></p>
		</div>
	</div>
	<div class="c2" id='paqueteAdjuntos'>
		<h2 id='adjuntosDat'></h2>
		<img id='archivoImg' src="">
	</div>
	<div id='paqueteTexto' class='paquete'>
		<h2 id='textoDat'></h2>
		<p id='texto'>
	</div>
</div>	
	
</div>
<script type="text/javascript">

	var _StatusEnvio='listo';
	
	var _Uid='<?php echo $Actividad;?>';
	var _Aid='<?php echo $ID;?>';
	var _Adata={};
	var _Pdat={};
	

	function consultaActividad(){
		if(_Aid<1){
			document.querySeelctor('body').innerHTML+="<h1>la actividad no fue llamada correctamnte<'h1>";
			return;
		}		
		
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
					_Adata=_res.data.actividad;				
					cargarActividad();
					consultaRegistros();
			}
		})
	}	
	consultaActividad();
	
	
	function cargarActividad(){		
		document.querySelector('#consigna').innerHTML=_Adata.consigna;
		document.querySelector('#marco').innerHTML=_Adata.marco;
		document.querySelector('#objeto').innerHTML=_Adata.objeto;
		
		document.querySelector('#modelo #paqueteCategoria').setAttribute('estado',_Adata.categAct);
		document.querySelector('#modelo #categDat').innerHTML=_Adata.categDat;
		document.querySelector('#modelo #paqueteTextobreve').setAttribute('estado',_Adata.trextobreveAct);
		document.querySelector('#modelo #textobreveDat').innerHTML=_Adata.textobreveDat;
		document.querySelector('#modelo #paqueteTexto').setAttribute('estado',_Adata.textoAct);
		document.querySelector('#modelo #textoDat').innerHTML=_Adata.textoDat;
		document.querySelector('#modelo #paqueteValor').setAttribute('estado',_Adata.valorAct);
		document.querySelector('#modelo #valorDat').innerHTML=_Adata.valorDat;
		document.querySelector('#modelo #paqueteAdjuntos').setAttribute('estado',_Adata.adjuntosAct);
		document.querySelector('#modelo #adjuntosDat').innerHTML=_Adata.adjuntosDat;
	}
	
	function consultaRegistros(){
		if(_StatusEnvio!='listo'){
			alert('sistema ocupado, reintente en unos segundos');
			console.log('el sistema de envió está acupado');
			return;
		}   
		_datos={};
		_datos["aid"]=_Aid;
	
		$.ajax({
			data: _datos,
			url:   'puntos_consulta.php',
			type:  'get',
			success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					_Pdat=_res;
					mostrarPuntos();				
					
			}
		})
	}
	
	function mostrarPuntos(){
		
		for(_np in _Pdat.features){
			_dat=_Pdat.features[_np].properties;
			_clon = document.querySelector('#modelo').cloneNode(true);
			_clon.removeAttribute('id');
			document.querySelector('#page').appendChild(_clon);
				
			_clon.querySelector('#rid').innerHTML=_dat.id;
			_clon.querySelector('#autor').innerHTML=_dat.nombreUsuario;
			_clon.querySelector('#fecha').innerHTML=_dat.fecha;
			_clon.querySelector('#valor').innerHTML=_dat.valor;
			_clon.querySelector('#texto').innerHTML=_dat.texto;
			
			
			_clon.querySelector('.punto').style.backgroundColor=_dat.style.color;
			_clon.querySelector('.punto').style.borderColor=_dat.style.color;
			_clon.querySelector('#categoriatx').innerHTML=_dat.categoriaNom;
			
			_clon.querySelector('#textobreve').innerHTML=_dat.categoriaNom;
			
			_clon.querySelector('#archivoImg').setAttribute('src',_dat.link);
			
		}
		
		
		
	}
						
		
</script>


</body>
