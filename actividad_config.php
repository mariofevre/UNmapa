<?php 
/**
* actividad_confing.php
*
* aplicación para configurar una actividad
*  
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	actividad
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU (agpl) 2014 TReCC SA
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
	echo "usuario no identificado";exit;
	header('Location: ./login.php');
}

// función de consulta de actividades a la base de datos 
include("./actividades_consulta.php");



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


// función para obtener la información de la actividad en cuestion 
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
		
if($Coordinacion!='activa'){
	echo "($UsuarioI == ".$Contenido['zz_AUTOUSUARIOCREAC'];
	echo "ERROR de Acceso 2";
	header('location: ./actividades.php');	//si no se es el autor de la actividad no se permite la configuración
}

//funcion para extraer la estructura de la tabla de almacenamineto de la actividad

$query="SHOW FULL COLUMNS FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`";
$Consulta = $Conec1->query($query);
while($row = $Consulta->fetch_assoc()){
	$Tabla[$row['Field']]=$row;
};
$Tabla['ACTcategorias']['Comment']='Menú de categorias';
$Tabla['ACTcategorias']['Type']='TablaHija';
//echo "<pre>";print_r($Tabla);echo "</pre>";


// el reingreso a esta dirección desde su propio formulario php crea o modifica un registro de actividad 
if(isset($_POST['accion'])){
	$accion =$_POST['accion'];
			
	if($_POST['accion']=='guardar'){
		
		foreach($Contenido as $k => $v){				
			if(substr($k,0,3)!='zz_'&&$k!='id'&&$k!='geometria'&&$k!='GEO'){										 
				if(isset($_POST[$k])){						
					$set.="`$k`='".$_POST[$k]."', ";						
				}else{						
					
				}					
			}
		}
		
		
		if($_POST['accionpub']=='Publicar'){
			$set.="`zz_PUBLICO`='1', ";
		}
		
		
		if($_POST['accionelim']=='Confirmo Eliminar'&&$Administracion=='activa'){
			$set.="`zz_borrada`='1', ";
		}		
				
					
		$set= substr($set,0,-2);					
		
		if($Coordinacion=='activa'){
			
			$query = "		
				UPDATE 
					`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
				SET
					$set
				WHERE `id` = '".$Actividad."'
				";
			$Consulta = $Conec1->query($query);
			echo $Conec1->error;
			$ID=$Actividad;
			
			//echo $query;
			//exit;
		}else{
			echo "no se encontraron permisos de edición de actividad para su usuario."; 
			exit;
		}
		
		//recarga los datos de la actividad
		$Contenido =  reset(actividadesconsulta($ID,$seleccion));
		
	}
}
//echo "<pre>";print_r($Contenido);echo "</pre>";
// medicion de rendimiento lamp 
	$starttime = microtime(true);

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
?>

	<title>UNmapa - Cofiguración de actividades</title>
	<?php include("./includes/meta.php");?>
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
		 	width:60px;
		 }
		 input.mes{
		 	width:30px;
		 }
		 input.dia{
		 	width:30px;
		 }
		 input#C_resumen{
		 	width:500px;
		}
		input.cambiado{
			background-color:#fdd;
			color:#d00;
		}
		#L_valorAct, #L_valorAct, #L_valorDat, #L_valorUni, 
		#L_textobreveAct, #L_textobreveDat, 
		#L_textoAct, #L_textoDat, 
		#L_adjuntosAct, #L_adjuntosExt, 
		#L_categAct, #L_categDat, #L_categLib{
			width:130px;
		}
		div.emasc:after{
			color:red;
			content:"\A"; white-space:pre; 
		}
		textarea{
			height: 100px;
		    width: 780px;
		    font-size:12px;
		}
		table{
			width: 780px;
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
	
	?>
	
	
	<div id="pageborde"><div id="page">

		<?php

			echo  "<h1>Configuración: <span class='menor'>de actividad Nº $Actividad : ".$Contenido['resumen']."</span></h1>";
		
			if($Coordinacion=='activa'){
				echo "<a href='./actividad.php?actividad=$ID'>acceder a la Actividad</a>";
			}
				
			echo "<br><a href='./actividad_usuarios.php?actividad=$ID'>acceder a la Gestión de Usuarios de la actividad</a>";
		
		?>

		<iframe id='mapa' name='mapa' src='./MAPAconfig.php?actividad=<?php echo $Actividad;?>&consulta=creararea'></iframe>
		
		<?php
			// formulario para agregar una nueva actividad		
		if($ID==''){
				echo "la actividad no fue llamada correctamnte";
		}else{
			// formulario para modificar una actividad 
			/*
			echo "<form id='adjuntador' enctype='multipart/form-data' method='post' action='./agrega_adjunto.php' target='cargaimagen'>";
			echo "<label title='el archivo de imagen aerea es representado en el mapa dentro de las coordenadas definidas más adelante.' style='position:relative;' class='upload'>";							
				echo "<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo de imagen aerea</span>";
				echo "<input id='uploadinput' style='position:relative;opacity:0;' type='file' name='upload' value='' onchange='this.parentNode.parentNode.submit();'></label>";
				echo "<input type='hidden' id='actividad' name='actividad' value='".$ID."'>";
				echo "<input type='hidden' id='tipo' name='tipo' value='img.png'>";
			echo "</form>";
			echo "<iframe id='cargaimagen' name='cargaimagen'></iframe>";
			
			$carpnum=str_pad($ID, 8, '0', STR_PAD_LEFT);
			$ruta="./documentos/actividades/".$carpnum."/img.png";
			$src='';
			if(file_exists($ruta)){$src="src='".$ruta."'";}
			echo "<img id='adjunto' $src>";
			*/
			
						
			echo "<form method='post' action='./actividad_config.php?actividad=$ID'>";
			echo "<input type='hidden' name='accion' value='guardar'>";
			
			echo "<input type='submit' value='guardar cambios'>";
			echo "<input type='submit' title='Al publicarse una actividad, permite la inscripción de participantes y la carga de datos' name='accionpub' value='Publicar'>";
			
			if($Administracion=='activa'){
				echo "<input type='button' title='Al eliminarse una actividad, esta se enviará a la papelera, no estará visible para ningún usuario' id='elim' value='Eliminar' onclick='this.style.display=\"none\";document.getElementById(\"elimC\").style.display=\"inline-block\";document.getElementById(\"elimNo\").style.display=\"inline-block\";'>";
				echo "<input type='button' style='display:none;' title='cancelar eliminación' id='elimNo' value='Cancelar' onclick='this.style.display=\"none\";document.getElementById(\"elimC\").style.display=\"none\";document.getElementById(\"elim\").style.display=\"inline-block\";'>";
				echo "<input type='submit' style='display:none;' title='Al eliminarse una actividad, esta se enviará a la papelera, no estará visible para ningún usuario' name='accionelim' value='Confirmo Eliminar' id='elimC'>";
			}
			echo "<input type='submit' title='Al duplicarse se generará una nueva actividad con igual configuración sin valores cargados' onclick='duplicar(event)' value='Duplicar'>";
			
			echo "<br>";
			echo "<input type='hidden' name='actividad' id='actividad' value='$ID'>";	
					
			foreach($Contenido as $k => $v){
				//echo $Tabla[$k]['Comment'].PHP_EOL;
				if($k=='imx0'){continue;}
				if($k=='imxF'){continue;}
				if($k=='imy0'){continue;}
				if($k=='imyF'){continue;}
				if($k=='abierta'){continue;}
				
				if(substr($k,0,3)!='zz_'&&$k!='id'&&$k!='geometria'&&$k!='GEO'){
					echo "<div class='emasc'>";
					if(isset($Tabla[$k])){
						echo "<label id='L_$k'>".$Tabla[$k]['Comment']."</label>";
						
						if($Tabla[$k]['Type']=='tinyint(1)'){
							if($v=='1'){$s="checked";}else{$s='';}		
							echo "<input type='hidden' name='$k' id='C_$k' value='$v'>";
							echo "<input $s type='checkbox' value='$v' campo='$k' onclick='check(this);'>";							
						}elseif($Tabla[$k]['Type']=='date'){
							echo "<input type='hidden' name='$k' id='C_$k' value='$v'>";
							echo "<input class='dia' campo='$k' id='C_".$k."_d' value='".dia($v)."' onchange='fechar(this);'>-";					
							echo "<input class='mes' campo='$k' id='C_".$k."_m' value='".mes($v)."' onchange='fechar(this);'>-";			
							echo "<input class='ano' campo='$k' id='C_".$k."_a' value='".ano($v)."' onchange='fechar(this);'> ";			
						
						}elseif($Tabla[$k]['Type']=='text'){
							echo "<textarea id='C_$k' name='$k'>$v</textarea>";
							
						}elseif($Tabla[$k]['Type']=='TablaHija'){
						
							echo "<input id='H_$k' name='' value='-agregar nuevo-' onclick='validarContenido(this);'>";
							echo "<input campo='nombre' accion='agrega' type='button' id='B_$k' onclick='crearRegHijo(this)' value='+'>";
							
							echo "<table id='T_$k'>";
							echo "<tr>";
								echo "<td>N</td>";
								foreach(reset($v) as $ck => $cv){
									if(substr($ck,0,2)!='id'&&substr($ck,0,3)!='zz_'){
										echo "<td>".$ck."</td>";
									}
								}
								echo "<td>cant</td>";
								echo "<td>editar</td>";
								echo "<td>fusionar</td>";
								echo "<td>filtrar</td>";
							echo" </tr>";
							$nn=0;
							unset($ncat);
							foreach($v as $cat){
								if($cat['zz_fusionadaa']==0){
								$nn++;
								$ncat[$nn]['id']=$cat['id'];
								$ncat[$nn]['nom']=$cat['nombre'];
								$icat[$cat['id']]=$nn;
								}
							}
							$nn=0;
							foreach($v as $cat){
								if($cat['zz_fusionadaa']==0){
									$nn++;
									$nnn=$nn;
								}else{
									$nnn='';
								}
								echo "<tr>";								
								echo "<td>$nnn</td>";
								foreach($cat as $ck => $cv){
									$val=$cv;
									if(substr($ck,0,2)!='id'&&substr($ck,0,3)!='zz_'){
										if(substr($ck,0,3)=='CO_'){
											$val="<div style='width:30px;height:10px;background-color:$cv;'></div>";
										}
										if($cat['zz_fusionadaa']!=0
											&& ($ck=='orden' || substr($ck,0,3)=='CO_')
										){
											$val='-';
										}
									echo "<td>$val</td>";
									}
								}
								echo "<td>".$Contenido['categoriaspuntos'][$cat['id']]."</td>";
								echo "<td><a target='_blank' href='agrega_f.php?salida=actividad_config&salidaid=".$Actividad."&tabla=".$k."&accion=cambia&id=".$cat['id']."' >editar</a></td>";
								echo "<td>";
								
									if($cat['zz_fusionadaa']!=0){// las categorias ya fusionadas se desfusionan antes de generar nuevas fusiones.
										$ns=$icat[$cat['zz_fusionadaa']];
										
										echo "<input class='fusion' type='button' onclick='liberarFusionCategoria(this,".$cat['id'].");' value='liberar de ".$ns."'>";
									}elseif($Contenido['categoriaspuntos'][$cat['id']]==0){ //las categorias sin puntos no merecen ser fusionadas
										echo "sin puntos";
									}else{
										echo "<select Corigen='".$cat['id']."' class='fusion' onchange='fusionarCategoriaA(this)'>";
										echo "<option value='0'>-fusionar a-</option>";
										foreach ($ncat as $n => $a){
											if($a['id']==$cat['id']){continue;}
											if($cat['zz_fusionadaa']==$a['id']){$s=selected;}else{$s='';}
											echo "<option $s value='".$a['id']."'>".$n. " - " . $a['nom']."</option>";
										}
										echo "</select>";
									}
								echo "</td>";
								echo "<td>";
									if($Contenido['categoriaspuntos'][$cat['id']]>0){
										echo "<input type='radio' name='filtro' value='male' onChange='filtrarmapa(\"".$cat['id']."\");'>";
									}
								echo "</td>";
								echo" </tr>";
							}
							echo "</table>";
						}else{
							echo "<input id='C_$k' name='$k' value='$v'>";
						}
					}else{
						echo "<label>$k</label><p>$v</p>";
					}
					echo "</div>";
					if($Tabla[$k]['Type']=='TablaHija'){echo "<br>";}
				}
			}
			echo "</form>";				
				
			$datosargumentacion = actividadesconsulta($ID);
			
			echo "</div>";
		}


		?>
	
	</div></div>
	
	<script type='text/javascript'>
	
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
						console.log(_res);
						window.location.assign('./actividad_config.php?actividad='+_res.data.nid);
					}else{
						alert('ocurrió algún error en la consulta');
					}
					
				}
			})
		}
	</script>
	
	<script type='text/javascript'>
		
		var _Adat={
			'x0': '<?php echo $Contenido['x0'];?>',
			'y0': '<?php echo $Contenido['y0'];?>',
			'xF': '<?php echo $Contenido['xF'];?>',
			'yF': '<?php echo $Contenido['yF'];?>'			
		}
		function validarContenido(_this){
			if(_this.value=='-agregar nuevo-'){
				_this.setAttribute('value','');
			}
		}
		
		
		function fusionarCategoriaA(_this){//completa formulario de fusion de categorías y lo envía
			if(_this.value>0){
				console.log(_this.value);
				document.getElementById('InModFusionDestino').value=_this.value;
				document.getElementById('InModFusionOrigen').value=_this.getAttribute('Corigen');
				document.getElementById('formFusionMod').submit();
				_this.parentNode.innerHTML='--fusionado--';
				
			}
		}

		function liberarFusionCategoria(_this,_origen){//completa formulario de fusion de categorías y lo envía
			if(_origen>0){
				document.getElementById('InModFusionDestino').value='0';
				document.getElementById('InModFusionOrigen').value=_origen;
				document.getElementById('formFusionMod').submit();
				_this.parentNode.innerHTML='--liberado--';
			}
		}			
	</script>
	
	<script type='text/javascript'>

		function filtrarmapa(_catid){

			document.getElementById('mapa').src='./MAPAactividad.php?actividad=<?php echo $Actividad;?>&consulta=creararea&filtrosi[]=categoria__'+_catid;
			
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
