<?php 
/**
* actividad_confing.php
*
* aplicación para configurar una actividad
*  
* @package    	UNmapa Herramienta pedágogica para la construccion colaborativa del territorio.  
* @subpackage 	actividad
* @author     	Universidad Nacional de Moreno
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on proyecto Plataforma Colectiva de Información Territorial: UBATIC2014
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
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


// verificación de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){
	echo "usuario no identificado";exit;
	header('Location: ./login.php');
}

// función de consulta de actividades a la base de datos 
// include("./actividades_consulta.php");



$ID = isset($_GET['actividad'])?$_GET['actividad'] : '';

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
	echo "ERROR de Acceso 1";exit;	
	header('location: ./actividades.php');	//si no hay una actividad definida esta página no debería consultarse
}


// filtro de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 
	$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	
	

?>

	<title>UNmapa - Cofiguración de actividades</title>
	<?php include("./includes/meta.php");?>
	
	<link rel="icon" href="./img/unmicon.ico">
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
	<link href="css/UNmapa.css" rel="stylesheet" type="text/css">		
	
	<style type='text/css'>
	table{
		border-collapse: collapse;
	}
	td{
		border:1px solid gray;
	}
	td, tr, th, table{
		font-size:11px;
	}
	tr[fusionada='si']{
		background-color:#bbb;
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
		 form{
		 	margin:0;
		 }
		 label{
		 	font-size:12px;
		 	width:200px;
		 	font-weight:normal;
		 	vertical-align:middle;
		 }
		 input.ano{
		 	width: 32px;
		 }
		 input.mes{
		 	width:20px;
		 }
		 input.dia{
		 	width:20px;
		 }
		 input#C_resumen{
		 	width:500px;
		}
		input.cambiado{
			background-color:#fdd;
			color:#d00;
		}
		
		div.emasc:after{
			color:red;
			content:"\A"; white-space:pre; 
		}
		textarea{
			height: 150px;
		    width: 250px;
		    font-size:12px;
		}
		textarea#C_consigna{
			width: 780px;			
		}
		table{
			width: 565px;
		}
		div.emasc{
			display:inline-block;
			border:1px solid #ccc;
			margin:1px;
			min-height:30px;
			 vertical-align: top;
		}
		div.emasc > input {
			vertical-align:middle;
		}
		#ventanaaccion{
			position:fixed;
			right:0px;
			top:350px;
			width:200px;
			height:300px;
		}	
		#elimC{
			position:relative;
			left:60px;
			color:#000;
			background-color:red;
		}	
		
select.fusion input, select.fusion {
    background-color: #eefbff;
    border-color: gray #fff #fff gray;
    border-style: solid;
    border-width: 0;
    color: #08afd9;
    font-family: inherit;
    height: 14px;
    margin: 0;
    vertical-align: middle;
    width: 85px;
    padding:0px;
}
input.fusion{
	padding:0px;
    background-color: #eefbff;
    color: #08afd9;
    font-family: inherit;
    height: 14px;
    margin: 0;
    vertical-align: middle;
    width: 85px;
    line-height:8px;
    cursor:pointer;
}

iframe#cargaimagen{
	display:none;
}
form#adjuntador{
	width:90px;
	position:absolute;
	top:370px;
	left:600px;
}
img#adjunto{
	position:absolute;
	top:370px;
	left:715px;
	height:50px;
}
input#uploadinput{
	width:90px;
	height:35px;
}
label.upload{
	height:35px;
}
label.upload:hover{
	background-color:#CEF6F5;
}



form#fusionarCat{
	display:none;
	position:fixed;
	width:400px;
	left:calc(50vw - 200px);
	height:200px;
	top:calc(50vh - 100px);
	background-color:#fff;
	border:2px solid #08AFD9;
	box-shadow:10px 10px 10px #000;
	z-index:100;
}
form#fusionarCat label{
	width:80px;
	text-align:right;
}
form#fusionarCat input{
	width:80px;
}
form#fusionarCat input[type="text"]{
	width:310px;
}
form#fusionarCat input[value="eliminar"]{
	background-color:#f00;
	color:#000;
}
form#fusionarCat input[value="eliminar"][disabled='disabled']{
	opacity:0.2;
}
form#fusionarCat textarea{
	width:310px;
	height:30px;
}


form#categoria{
	display:none;
	position:fixed;
	width:400px;
	left:calc(50vw - 200px);
	height:200px;
	top:calc(50vh - 100px);
	background-color:#fff;
	border:2px solid #08AFD9;
	box-shadow:10px 10px 10px #000;
	z-index:100;
}
form#categoria label{
	width:80px;
	text-align:right;
}
form#categoria input{
	width:80px;
}
form#categoria input[type="text"]{
	width:310px;
}
form#categoria input[value="eliminar"]{
	background-color:#f00;
	color:#000;
}
form#categoria input[value="eliminar"][disabled='disabled']{
	opacity:0.2;
}
form#categoria textarea{
	width:310px;
	height:30px;
}
.emasc h2{
	float:left;
	width:210px;
	margin-top:3px;
	margin-bottom:3px;
}
.emasc h2 label{
	width:180px;
	font-size:15px;
	font-weight:bold;
}

#L_hasta, #L_desde, #L_nivel, #L_categLib, #L_categDat, #L_adjuntosDat, #L_valorDat, #L_valorUni{
	width:130px;	
}
#L_objeto{
	width:230px;
}

.emasc[estado="inactivo"]{
	width:250px;
	
}

.emasc[estado="inactivo"] .emasc{
	display:none;
}

.emasc table td div{
	width:30px;
	height:10px;
}

td.link:hover{
	background-color:#08afd9;
	color:#fff;
}
td.link{
	cursor:pointer;
}

.desc{
	width:250px;
}
	</style>

	
</head>

<body>
	<script  type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
	<?php
	include('./includes/encabezado.php');
	
	if($ID!=''){
		echo "
		<div class='recuadro' id='recuadro2'>		
			<h4>Puntos visualizados del banco de datos</h4>
			<ul id='puntosdebase'>		
			</ul>	
		</div>
		<div class='recuadro' id='recuadro3' >
			<h4>Puntos relevados en esta actividad</h4>
			<ul id='puntosdeactividad'>		
			</ul>
		</div>
		<iframe src='' id='ventanaaccion' name='ventanaaccion'></iframe>
		";
	}
	
	
	if($ID==''){
			echo "la actividad no fue llamada correctamnte";
			exit;
	}
	
	?>
	
	
	<div id="pageborde"><div id="page">
		<h1>Configuración: <span class='menor'>de actividad Nº <?php echo $Actividad." : ". $Contenido['resumen'];?></span></h1>
		<a href='./actividad.php?actividad=<?php echo $ID;?>'>acceder a la Actividad</a>
							
		<?php
			if($Coordinacion=='activa'){	
				echo "<br><a href='./actividad_usuarios.php?actividad=$ID'>acceder a la Gestión de Usuarios de la actividad</a>";
			}			
		?>
			
			<iframe id='mapa' name='mapa' src='./MAPAconfig.php?actividad=<?php echo $ID;?>&consulta=creararea'></iframe>
			
			
			<form id='categoria'>
				<h2>Categoria</h2>
				<input type='hidden' name='idcat'>
				<label>nombre:</label><input type='text' name='nombre'><br>
				<label>descripción:</label><textarea name='descripcion'></textarea><br>
				<label>orden:</label><input type='number' name='orden'>
				<label>color:</label><input type='color' name='CO_color'>	<br>
				<br>
				<input type='button' name='accion' value='guardar' onclick='enviarCategoria();'>
				<input type='button' name='accion' value='cancelar' onclick='this.parentNode.style.display="none";'>
				<input type='button' name='accion' value='eliminar' onclick='eliminarCategoria();'>
			</form>
			
			<form id='fusionarCat'>
				<h2>Fusionar Categoria: <span id="nom"></span> a:</h2>
				<input type='hidden' name='idcat'>
				<div>La categoria fusionada ya no será visible y sus puntos serán asignados a su capa destino</div>
				<select name='idcatDest'>
					<option>-elegir una categoria-</option>
				</select>
				<br>
				<input type='button' name='accion' value='fusionar' onclick='enviarFusion();'>
				<input type='button' name='accion' value='cancelar' onclick='this.parentNode.style.display="none";'>
			</form>
			
							
			<form id='config' method='post' action='' onsubmit='enviarFormulario(event);'>
				<input type='hidden' name='actividad' id='actividad' value='<?php echo $ID;?>'>
				<input type='hidden' name='accion' value='guardar'>
				<input type='submit' value='guardar cambios'>
				<input type='button' 
					title='Al publicarse una actividad, permite la inscripción de participantes y la carga de datos' 
					value='Publicar'
					onclick='publicarActividad(event);'
				>
				<input type='hidden' name='accionpub' value=''>
				
				<input type='button' 
					title='Al eliminarse una actividad, esta se enviará a la papelera, no estará visible para ningún usuario' 
					id='elim' value='Eliminar' 
					onclick='eliminarActividad(event);'
				>
				<input type='hidden' name='accionelim' value=''>
				
				<input type='submit' title='Al duplicarse se generará una nueva actividad con igual configuración sin valores cargados' onclick='duplicar(event)' value='Duplicar'>
				
				<input type='button' onclick='hacerRectangulo()' value='definir area rectangular'>
				<input type='button' onclick='hacerPoligono()' value='definir area de poligono'>
				
				<br>
				<input type='hidden' name='actividad' id='actividad' value='<?php echo $ID;?>'>
				<input type='hidden' id='C_x0' name='x0' value=''>
				<input type='hidden' id='C_y0' name='y0' value=''>
				<input type='hidden' id='C_xF' name='xF' value=''>
				<input type='hidden' id='C_yF' name='yF' value=''>
				<input type='hidden' id='C_geometria' name='geometria' value=''>
				
				<div class='emasc'>
					<label id='L_resumen'>nombre o resumen de la actividad</label>
					<input id='C_resumen' name='resumen' value=''>
				</div>
								
				<div class='emasc'>
					<label id='L_nivel'>nivel academico (grado, posgrado, secundario)</label>
					<input id='C_nivel' name='nivel' value=''>
				</div>
				
				<div class='emasc'>
					<label id='L_desde'> Fecha desde la cual la actividad se encuentra activa</label>
					<input type='hidden' name='desde' id='C_desde' value='0000-00-00'>
					<input class='dia' campo='desde' id='C_desde_d' value='00' onchange='fechar(this);'>-<input class='mes' campo='desde' id='C_desde_m' value='00' onchange='fechar(this);'>-<input class='ano' campo='desde' id='C_desde_a' value='0000' onchange='fechar(this);'> 
				</div>
				
				<div class='emasc'>
					<label id='L_hasta'> Fecha hasta la cual la actividad se encuentra activa</label>
					<input type='hidden' name='hasta' id='C_hasta' value='0000-00-00'>
					<input class='dia' campo='hasta' id='C_hasta_d' value='00' onchange='fechar(this);'>-<input class='mes' campo='hasta' id='C_hasta_m' value='00' onchange='fechar(this);'>-<input class='ano' campo='hasta' id='C_hasta_a' value='0000' onchange='fechar(this);'> 
				</div>
				
				<div class='emasc'>
					<label id='L_consigna'>Copia textual de la consigna expresada a los estudiantes.</label>
					<textarea id='C_consigna' name='consigna'></textarea>
				</div>

				
				<div class='emasc'>
					<h2>
						<label id='L_adjuntosAct'>La actividad permite adjuntos y links</label>
						<input type='hidden' name='adjuntosAct' id='C_adjuntosAct' value=''>
						<input checked type='checkbox' campo='adjuntosAct' onclick='check(this);'>
					</h2>
				
				
					<div class='emasc'>
						<label id='L_adjuntosDat'>Que debe adjuntar o vincular el participante</label>
						<input id='C_adjuntosDat' name='adjuntosDat' value=''>
					</div>
					
					<div class='emasc'>
						<label id='L_adjuntosExt'>Permite vínculos externos</label>
						<input type='hidden' name='adjuntosExt' id='C_adjuntosExt' value=''>
						<input  type='checkbox' campo='adjuntosExt' onclick='check(this);'>
					</div>
					
				</div>
				
				<br>
				
				<div class='emasc'>
					<h2>
						<label id='L_valorAct'>La actividad permite cargas valores numéricos</label>
						<input type='hidden' name='valorAct' id='C_valorAct' value=''>
						<input  type='checkbox' campo='valorAct' onclick='check(this);'>
					</h2>
					<div class='emasc'>
						<label id='L_valorDat'>Significado del valor numérico cargado</label>
						<input id='C_valorDat' name='valorDat' value=''>
					</div>
					
					<div class='emasc'>
						<label id='L_valorUni'>Unidad de medida para el valor numérico</label>
						<input id='C_valorUni' name='valorUni' value=''>
					</div>
				</div>
				
				<br>
				
				<div class='emasc'>
					<h2>
						<label id='L_textobreveAct'>La actividad permite cargar textos breves</label>
						<input type='hidden' name='textobreveAct' id='C_textobreveAct' value=''>
						<input checked type='checkbox' campo='textobreveAct' onclick='check(this);'>
					</h2>
					<div class='emasc'>
						<label id='L_textobreveDat'>Tipo de dato capturado el texto breve</label>
						<input id='C_textobreveDat' name='textobreveDat' value=''>
					</div>
				</div>
				
				<br>
				
				<div class='emasc'>
					<h2>
					<label id='L_textoAct'>La actividad permite cargar textos extensos</label>
					<input type='hidden' name='textoAct' id='C_textoAct' value=''>
					<input checked type='checkbox' campo='textoAct' onclick='check(this);'>
					</h2>
					<div class='emasc'>
						<label id='L_textoDat'>El tipo de dato a capturar por el texto extenso</label>
						<input id='C_textoDat' name='textoDat' value=''>
					</div>
					
				</div>
				
				<br>
				
				<div class='emasc'>
					<h2>
					<label id='L_categAct'>La actividad permite cargar categorías</label>
					<input type='hidden' name='categAct' id='C_categAct' value='1'>
					<input checked type='checkbox' value='1' campo='categAct' onclick='check(this);'>
					</h2>
					<div class='emasc'>
					<label id='L_categDat'>Criterio de categorización</label>
					<input id='C_categDat' name='categDat' value=''>
					</div><div class='emasc'>
						<label id='L_categLib'>Categorías libre. permite crear nuevas</label>
						<input type='hidden' name='categLib' id='C_categLib' value='0'>
						<input  type='checkbox' value='0' campo='categLib' onclick='check(this);'>
					</div>
					<div class='emasc'>
						<label id='L_ACTcategorias'>Menú de categorias</label>
						<input id='H_ACTcategorias' name='' value='-agregar nuevo-' onclick='validarContenido(this);'>
						<input campo='nombre' accion='agrega' type='button' id='B_ACTcategorias' onclick='crearRegHijo(this)' value='+'>
						<table id='T_ACTcategorias'>
							<tr><td>N</td><td>nombre</td><td class='desc'>descripcion</td><td>orden</td><td>color</td><td>cant</td><td>fusionar</td><td>ver</td> </tr>
						</table>
					</div>
				</div>
				
				<br>
				
				<div class='emasc'>
					<label id='L_objeto'> el Objeto de estudio, relevamiento o captura.</label>
					<br>
					<textarea id='C_objeto' name='objeto'></textarea>
				</div>
				
				
				<div class='emasc'>
					<label id='L_resultados'>resultados obtenidos</label>
					<br>
					<textarea id='C_resultados' name='resultados'></textarea>
				</div>
				
				<div class='emasc'>
					<label id='L_marco'>marco de la actividad</label>
					<br>
					<textarea id='C_marco' name='marco'></textarea>
				</div>

				
			</form>
		</div>	
	</div></div>
	

	</div></div>

<script type='text/javascript'>
	
	_Aid='<?php echo $ID;?>';
	_Adata={};
	function consultarActividad(){		
		_datos={
			'actividad':_Aid				
		};
		
		$.ajax({
			data: _datos,
			url:   './ACT_consulta.php',
			type:  'post',
			success:  function (response){
				
				var _res = $.parseJSON(response);
				//console.log(_res);
				
				for(_nm in _res.mg){
					alert(_res.mg[_nm]);
				} 
				if(_res.res=='exito'){
					console.log(_res);
					_Adata=_res.data[_Aid];
					cargarActividad();
				}else{
					alert('ocurrió algún error en la consulta');
				}
				
			}
		})
	}
	
	consultarActividad();
	
	function cargarActividad(){
		
		_form=document.querySelector('#page form#config');
		
		_form.querySelector('#C_resumen').value=_Adata.resumen;
		
		_form.querySelector('#C_nivel').value=_Adata.nivel;
		
		_form.querySelector('#C_desde').value=_Adata.desde;
		_dd=_Adata.desde.split('-');
		_form.querySelector('#C_desde_a').value=_dd[0];
		_form.querySelector('#C_desde_m').value=_dd[1];
		_form.querySelector('#C_desde_d').value=_dd[2];
		
		_form.querySelector('#C_hasta').value=_Adata.hasta;
		_hh=_Adata.hasta.split('-');
		_form.querySelector('#C_hasta_a').value=_hh[0];
		_form.querySelector('#C_hasta_m').value=_hh[1];
		_form.querySelector('#C_hasta_d').value=_hh[2];
		
		_form.querySelector('#C_consigna').value=_Adata.consigna;
		
		_form.querySelector('#C_x0').value=_Adata.x0;
		_form.querySelector('#C_y0').value=_Adata.y0;
		_form.querySelector('#C_xF').value=_Adata.xF;
		_form.querySelector('#C_yF').value=_Adata.yF;
		_form.querySelector('#C_geometria').value=_Adata.geometria;
		
		_form.querySelector('#C_adjuntosAct').value=_Adata.adjuntosAct;
		if(_Adata.adjuntosAct==1){
			_form.querySelector('input[campo="adjuntosAct"]').parentNode.parentNode.setAttribute('estado','activo');
			_form.querySelector('input[campo="adjuntosAct"]').checked=true;
		}else{
			_form.querySelector('input[campo="adjuntosAct"]').parentNode.parentNode.setAttribute('estado','inactivo');
			_form.querySelector('input[campo="adjuntosAct"]').checked=false;
		}		
		_form.querySelector('#C_adjuntosDat').value=_Adata.adjuntosDat;
		
		
		_form.querySelector('#C_valorAct').value=_Adata.valorAct;
		if(_Adata.valorAct==1){
			_form.querySelector('input[campo="valorAct"]').parentNode.parentNode.setAttribute('estado','activo');
			_form.querySelector('input[campo="valorAct"]').checked=true;
		}else{
			_form.querySelector('input[campo="valorAct"]').parentNode.parentNode.setAttribute('estado','inactivo');
			_form.querySelector('input[campo="valorAct"]').checked=false;
		}		
		_form.querySelector('#C_valorDat').value=_Adata.valorDat;
		_form.querySelector('#C_valorUni').value=_Adata.valorUni;
			
				
		_form.querySelector('#C_textobreveAct').value=_Adata.textobreveAct;
		if(_Adata.textobreveAct==1){
			_form.querySelector('input[campo="textobreveAct"]').parentNode.parentNode.setAttribute('estado','activo');
			_form.querySelector('input[campo="textobreveAct"]').checked=true;
		}else{
			_form.querySelector('input[campo="textobreveAct"]').parentNode.parentNode.setAttribute('estado','inactivo');
			_form.querySelector('input[campo="textobreveAct"]').checked=false;
		}		
		_form.querySelector('#C_textobreveDat').value=_Adata.textobreveDat;
		
		
		_form.querySelector('#C_textoAct').value=_Adata.textoAct;
		if(_Adata.textoAct==1){
			_form.querySelector('input[campo="textoAct"]').parentNode.parentNode.setAttribute('estado','activo');
			_form.querySelector('input[campo="textoAct"]').checked=true;
		}else{
			_form.querySelector('input[campo="textoAct"]').parentNode.parentNode.setAttribute('estado','inactivo');
			_form.querySelector('input[campo="textoAct"]').checked=false;
		}		
		_form.querySelector('#C_textoDat').value=_Adata.textoDat;


		_form.querySelector('#C_categAct').value=_Adata.categAct;
		if(_Adata.categAct==1){
			_form.querySelector('input[campo="categAct"]').parentNode.parentNode.setAttribute('estado','activo');
			_form.querySelector('input[campo="categAct"]').checked=true;
		}else{
			_form.querySelector('input[campo="categAct"]').parentNode.parentNode.setAttribute('estado','inactivo');
			_form.querySelector('input[campo="categAct"]').checked=false;
		}		
		_form.querySelector('#C_categDat').value=_Adata.categDat;
		
		
		if(_Adata.categLib==1){
			_form.querySelector('input[campo="categLib"]').checked=true;
		}else{
			_form.querySelector('input[campo="categLib"]').checked=false;
		}		
		_form.querySelector('#C_categLib').value=_Adata.categLib;		
		
		_n=0;
		for(_idc in _Adata.categorias){
			
		
			_dat=_Adata.categorias[_idc];
			if(_dat.zz_fusionadaa>0){_n++;}
			
			
			_fila=document.createElement('tr');
			_fila.setAttribute('idcat',_idc);
			
			if(_dat.zz_fusionadaa>'0'){_fila.setAttribute('fusionada','si');}
			
			document.querySelector('#T_ACTcategorias').appendChild(_fila);
			
			_cel=document.createElement('td');
			if(_dat.zz_fusionada>'0'){_cel.innerHTML=_n;}
			_fila.appendChild(_cel);
			
			_cel=document.createElement('td');
			_cel.setAttribute('class','link');
			_cel.setAttribute('campo','nombre');
			_cel.setAttribute('onclick','formularCategoria(this.parentNode.getAttribute("idcat"))');
			_cel.innerHTML=_dat.nombre;
			_fila.appendChild(_cel);
			
			_cel=document.createElement('td');
			_cel.setAttribute('campo','descripcion');
			_cel.innerHTML=_dat.descripcion;
			if(_dat.zz_fusionadaa>'0'){
				_cel.innerHTML="fusinada a "+_Adata.categorias[_dat.zz_fusionadaa].nombre;
			}
			
			_fila.appendChild(_cel);
			
			_cel=document.createElement('td');
			_cel.innerHTML=Math.round(_dat.orden);
			_cel.setAttribute('campo','orden');
			_fila.appendChild(_cel);

			_cel=document.createElement('td');
			_cel.innerHTML='<div style="background-color:'+_dat.CO_color+'">';
			_cel.setAttribute('campo','CO_color');
			_fila.appendChild(_cel);
			
			_cel=document.createElement('td');
			_cel.innerHTML=_dat.cant;
			_fila.appendChild(_cel);
			
			
			
			_cel=document.createElement('td');
			_cel.innerHTML="<a onclick='fusionarCategoriaA(this)'>a..</a>";
			
			if(_dat.zz_fusionadaa>'0'){
				_cel.innerHTML="<a onclick='liberarFusionCategoria(this)'>liberar</a>";
			}
			_fila.appendChild(_cel);
			
			_cel=document.createElement('td');
			_cel.innerHTML="<input type='radio' name='filtro' value='male' onChange='filtrarmapa(\""+_idc+"\");'>";
			_fila.appendChild(_cel);
		}
			
			
				
		_form.querySelector('#C_objeto').value=_Adata.objeto;
		_form.querySelector('#C_resultados').value=_Adata.resultados;
		_form.querySelector('#C_marco').value=_Adata.marco;
		
		document.getElementById('mapa').src='./MAPAconfig.php?actividad='+_Aid+'&consulta=creararea';
	}
		
	function enviarFormulario(_event){
		_event.preventDefault();
		
		_form=document.querySelector('form#config');
		_imps=_form.querySelectorAll('input, textarea, select');
		_params={};
		for(_ni in _imps){
			if(typeof _imps[_ni] != 'object'){continue;}
			_campo=_imps[_ni].getAttribute('name');
			if(_campo==''){continue;}
			if(_campo==null){continue;}
			_valor=_imps[_ni].value;
			_params[_campo]=_valor;
		}
		$.ajax({
			data: _params,
			url:   './ACT_ed_config.php',
			type:  'post',
			success:  function (response){
				
				var _res = $.parseJSON(response);
				//console.log(_res);
				
				for(_nm in _res.mg){
					alert(_res.mg[_nm]);
				} 
				if(_res.res=='exito'){
					alert('cambios guardados');
					console.log(_res);
					//window.location.reload();
				}else{
					alert('ocurrió algún error en la consulta');
				}
				
			}
		})
		
		
	}
	
	function publicarActividad(_event){
		if(confirm('Al publicarse una actividad esta pasa a ser visible por todos los usuarios. Si un usuario ingresa entre las fechas de inicio y fin, podrá cargar datos \n ¿Confirmás publicar esta actividad?')){
			document.querySelector('form#config input[name="accionpub"]').value='Publicar';
			enviarFormulario(_event);
		}
	}
	
	function eliminarActividad(_event){
		if(confirm('Al eliminarse una actividad, esta se enviará a la papelera, no estará visible para ningún usuario. \n ¿Confirmás eliminar esta actividad?')){
			document.querySelector('form#config input[name="accionelim"]').value='Confirmo Eliminar';
			enviarFormulario(_event);
		}
	}

</script>
	
	
		
	<script type='text/javascript'>
		function hacerPoligono(){
			_cont=document.getElementById('mapa').contentWindow;
			_cont.addInteractionPol();
			//_cont.mapa.removeInteraction(draw);
			//_cont.mapa.addInteraction(drawPol);		
		}
		function hacerRectangulo(){
			
			_cont=document.getElementById('mapa').contentWindow;
			_cont.addInteraction();
			//_cont.mapa.removeInteraction(draw);
			//_cont.mapa.addInteraction(drawPol);		
		}
		
	
		_Aid='<?php echo $ID;?>';
		
		function duplicar(_event){
			
			_event.preventDefault();
			_datos={
				'accion':'duplicar',
				'dupid':_Aid				
			};
			
			$.ajax({
				data: _datos,
				url:   './actividades_crear.php',
				type:  'post',
				success:  function (response){
					
					var _res = $.parseJSON(response);
					//console.log(_res);
					
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					} 
					if(_res.res=='exito'){
						window.location.assign('./actividad_config.php?actividad='+_res.data.nid);
					}else{
						alert('ocurrió algún error en la consulta');
					}
					
				}
			})
		}
	</script>
	
	<script type='text/javascript'>
		
		function validarContenido(_this){
			if(_this.value=='-agregar nuevo-'){
				_this.setAttribute('value','');
			}
		}
		
		
		function fusionarCategoriaA(_this){//completa formulario de fusion de categorías y lo envía
			_idcat=_this.parentNode.parentNode.getAttribute('idcat');
			if(_idcat>0){
				
				document.getElementById('fusionarCat').style.display='block';
				console.log(_idcat);
				_sel=document.querySelector('#fusionarCat input[name="idcat"]').value=_idcat;
				_sel=document.querySelector('#fusionarCat select');
				_sel.innerHTML='<option>-elegir-</option>';
				for(_cid in _Adata.categorias){
					if(_cid==_idcat){continue;}
					_op=document.createElement('option');
					_op.value=_cid;
					_op.innerHTML=_Adata.categorias[_cid].nombre;
					_sel.appendChild(_op);
				}
				
				document.getElementById('formFusionMod').submit();
				_this.parentNode.innerHTML='--fusionado--';
				
			}
		}

	
		function liberarFusionCategoria(_this,_origen){//completa formulario de fusion de categorías y lo envía
			if(!confirm('¿Segure de liberar esta categoría?')){return;}
			_param={
				'idact':_Aid,
				'idcat':_this.parentNode.parentNode.getAttribute("idcat"),
				'idcatDest':''
			}
			$.ajax({
				data: _param,
				url:   './ACT_ed_fusion.php',
				type:  'post',
				error:function (response){alert('error al conectarse al servidor');},
				success:  function (response){
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					} 

					if(_res.res!='exito'){alert('ocurrió algún error en la consulta');return;}

					_Adata.categorias[_res.data.id]=_res.data;
					console.log('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila=document.querySelector('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					document.querySelector('form#categoria').style.display='none';					
				}
			})
		
			
		}			
	</script>
	
	<script type='text/javascript'>

		function filtrarmapa(_catid){

			//document.getElementById('mapa').src='./MAPAactividad.php?actividad=<?php echo $Actividad;?>&consulta=creararea&filtrosi[]=categoria__'+_catid;
			document.getElementById('mapa').src='./MAPAconfig.php?actividad=<?php echo $Actividad;?>&consulta=creararea&filtrosi[]=categoria__'+_catid;
			
			window.scrollTo(0,0);			
		}
			
		function check(_this){//carga el valor en el campo oculto
			_c=_this.getAttribute('campo');
			_ch=_this.checked;
			if(_this.checked){
				document.getElementById('C_'+_c).value=1;
			}else{
				document.getElementById('C_'+_c).value=0;
			}
			
			if(_this.parentNode.tagName.toLowerCase()=='h2'){
				if(_this.checked){
					_this.parentNode.parentNode.setAttribute('estado','activo');
				}else{
					_this.parentNode.parentNode.setAttribute('estado','inactivo');
				}	
				
			}
			
			
		}

		function fechar(_this){// carga el valor en el campo oculto de fecha completa
			_c=_this.getAttribute('campo');
			_vd=document.getElementById('C_'+_c+'_d').value;
			_vm=document.getElementById('C_'+_c+'_m').value;
			_va=document.getElementById('C_'+_c+'_a').value;
			document.getElementById('C_'+_c).value=_va+'-'+_vm+'-'+_vd;
			
		}	
		
		function crearRegHijo(_this){ //crea en la tabla de referencia un registro
			_c=_this.getAttribute('id');
			_campo=_this.getAttribute('campo');
			_tabla=_c.substring(2,_c.length);
			_accion=_this.getAttribute('accion');
			_valor=document.getElementById('H_'+_tabla).value;
			
			_form=document.getElementById('formMod');
			document.getElementById('InModAcc').setAttribute('value',_accion);
			document.getElementById('InModTab').setAttribute('value',_tabla);
			document.getElementById('InModDat').setAttribute('name',_campo);
			document.getElementById('InModDat').setAttribute('value',_valor);
			document.getElementById('InModTabHtml').setAttribute('value','T_'+_tabla);
			
			_form.submit();
		}	
		
		
		
		function formularCategoria(_idcat){
			_form=document.querySelector('form#categoria');
			_form.style.display='block';
			_cdat=_Adata.categorias[_idcat];
			_form.querySelector('input[name="nombre"]').value=_cdat.nombre;
			_form.querySelector('textarea[name="descripcion"]').value=_cdat.descripcion;
			_form.querySelector('input[name="idcat"]').value=_idcat;
			_co=_cdat.CO_color.replace('rgb(','');
			_co=_co.replace(')','');
			_co=_co.split(',');
			_hex=rgbToHex(parseInt(_co[0]), parseInt(_co[1]), parseInt(_co[2]));
			_form.querySelector('input[name="CO_color"]').value=_hex;
			_form.querySelector('input[name="orden"]').value=_cdat.orden;
			if(_cdat.cant>0){
				_form.querySelector('input[value="eliminar"]').setAttribute('disabled','disabled');
			}else{
				_form.querySelector('input[value="eliminar"]').removeAttribute('disabled');
			}
			
		}
		
		
		
		function eliminarCategoria(){
			if(!confirm('Eliminamos esta categoría?')){return;}
			_form=document.querySelector('form#categoria');
			
			_param={
				'idact':_Aid,
				'idcat':_form.querySelector('input[name="idcat"]').value
			}
			
			$.ajax({
				data: _param,
				url:   './ACT_elim_categoria.php',
				type:  'post',
				error:function (response){alert('error al conectarse al servidor');},
				success:  function (response){
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					} 
					
					if(_res.res!='exito'){alert('ocurrió algún error en la consulta');return;}
					
					delete(_Adata.categorias[_res.data.id]);
					
					_fila=document.querySelector('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila.parentNode.removeChild(_fila);
					
					
					document.querySelector('form#categoria').style.display='none';
				}
			})
			
		}
		
		
		
		
		function enviarFusion(){
			_form=document.querySelector('form#fusionarCat');
			_form.style.display='none';
			
			_param={
				'idact':_Aid,
				'idcat':_form.querySelector('[name="idcat"]').value,
				'idcatDest':_form.querySelector('[name="idcatDest"]').value
			}
			$.ajax({
				data: _param,
				url:   './ACT_ed_fusion.php',
				type:  'post',
				error:function (response){alert('error al conectarse al servidor');},
				success:  function (response){
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					} 

					if(_res.res!='exito'){alert('ocurrió algún error en la consulta');return;}

					_Adata.categorias[_res.data.id]=_res.data;
					console.log('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila=document.querySelector('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila.querySelector('td[campo="nombre"]').innerHTML=_res.data.nombre;
					_fila.querySelector('td[campo="descripcion"]').innerHTML=_res.data.descripcion;
					_fila.querySelector('td[campo="CO_color"] div').style.backgroundColor=_res.data.CO_color;
					_fila.querySelector('td[campo="orden"]').innerHTML=_res.data.orden;

					document.querySelector('form#categoria').style.display='none';					
				}
			})
		}
		
		
		function enviarCategoria(){
			_form=document.querySelector('form#categoria');
			_c=hexToRgb(_form.querySelector('input[name="CO_color"]').value);
			_color='rgb('+_c.r+','+_c.g+','+_c.b+')';

			_param={
				'idact':_Aid,
				'nombre':_form.querySelector('input[name="nombre"]').value,
				'idcat':_form.querySelector('input[name="idcat"]').value,
				'descripcion':_form.querySelector('textarea[name="descripcion"]').value,
				'CO_color':_color,
				'orden':_form.querySelector('input[name="orden"]').value,
			}

			$.ajax({
				data: _param,
				url:   './ACT_ed_categoria.php',
				type:  'post',
				error:function (response){alert('error al conectarse al servidor');},
				success:  function (response){
					var _res = $.parseJSON(response);
					//console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					} 

					if(_res.res!='exito'){alert('ocurrió algún error en la consulta');return;}

					_Adata.categorias[_res.data.id]=_res.data;
					console.log('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila=document.querySelector('table#T_ACTcategorias tr[idcat="'+_res.data.id+'"]');
					_fila.querySelector('td[campo="nombre"]').innerHTML=_res.data.nombre;
					_fila.querySelector('td[campo="descripcion"]').innerHTML=_res.data.descripcion;
					_fila.querySelector('td[campo="CO_color"] div').style.backgroundColor=_res.data.CO_color;
					_fila.querySelector('td[campo="orden"]').innerHTML=_res.data.orden;

					document.querySelector('form#categoria').style.display='none';					
				}
			})
		}
		
		
		
		function hexToRgb(hex) {
		    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
		    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
		        return r + r + g + g + b + b;
		    });
		
		    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		    return result ? {
		        r: parseInt(result[1], 16),
		        g: parseInt(result[2], 16),
		        b: parseInt(result[3], 16)
		    } : null;
		}
		
		function componentToHex(c) {
		    var hex = c.toString(16);
		    return hex.length == 1 ? "0" + hex : hex;
		}
		
		function rgbToHex(r, g, b) {
		    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
		}

	</script>

	
	<form target='ventanaaccion' id='formMod' action='./agrega.php' method='post'>
		<input type='hidden' id='InModAcc' name='accion' value=''>
		<input type='hidden' id='InModTab'  name='tabla' value=''>		
		<input type='hidden' id='InModDat'  name='' value=''>				
		<input type='hidden' id='InModAct'  name='id_p_actividades_id' value='<?php echo $ID;?>'>
		<input type='hidden' id='InModTabHtml'  name='TablaHtml' value='<?php echo $ID;?>'>
	</form>
	
	
	<form target='ventanaaccion' id='formFusionMod' action='./cambia.php' method='post'>
		<input type='hidden' id='InModFusionAcc' name='accion' value='cambia'>
		<input type='hidden' id='InModFusionTab'  name='tabla' value='ACTcategorias'>
		<input type='hidden' id='InModFusionOrigen'  name='id' value=''>		
		<input type='hidden' id='InModFusionDestino'  name='zz_fusionadaa' value=''>
	</form>
	
	<?php
	include('./_serverconfig/pie.php');
	?>
</body>
