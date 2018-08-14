<?php 
/**
* actividad_usuarios.php
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

	$query="
	SELECT 
		`SISroles`.`id`,
	    `SISroles`.`nombre`,
	    `SISroles`.`descripción`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISroles`
	order by id desc
	";
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	//echo $query;
	
	while($row = $Consulta->fetch_assoc()){
		$Roles[$row['id']]=$row;
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
	echo "ERROR de Acceso 1";
	header('location: ./actividades.php');	//si no hay una actividad definida esta página no debería consultarse
}


// función para obtener la información de la actividad en cuestion 
$Contenido =  reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Contenido['Acc'][3]);echo "</pre>";


if($UsuarioI==$Contenido['zz_AUTOUSUARIOCREAC']){
	$Administracion='activa';
	$AdministracionElim='activa';
	$Coordinacion='activa';
	$UsuarioAcc=3;
}else{
	foreach($Contenido['Acc'][2] as $acc => $accdata){
		if($accdata['id_usuarios']==$UsuarioI){
			$Coordinacion='activa';
			$UsuarioAcc=2;
		}
	}
	foreach($Contenido['Acc'][3] as $acc => $accdata){
		print_r($accdata);
		if($accdata['id_usuarios']==$UsuarioI){
			$Administracion='activa';
			$Coordinacion='activa';
			$UsuarioAcc=3;
		}
	}		
}


if($Coordinacion!='activa'){
	echo "<h2>Error en el acceso, no se identificó nivel ".$Roles[2]['nombre']." o  más para su usuario en esta actividad.</h2>";
	exit;
}


$Actividad=reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Actividad);echo "</pre>";
if($Actividad['zz_PUBLICO']!='1'&&$Actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){
	echo "<h2>Error en el acceso, esta actividad no se encuentra aún publicada y usted no se encuentra registrado como autor de la misma.</h2>";
	exit;
}

//funcion para extraer la estructura de la tabla de almacenamineto de la actividad
$query="SHOW FULL COLUMNS FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`";

$Consulta = $Conec1->query($query);
while($row = $Consulta->fetch_assoc()){
	$Tabla[$row['Field']]=$row;
};
$Tabla['ACTcategorias']['Comment']='Menu de categorias';
$Tabla['ACTcategorias']['Type']='TablaHija';
//print_r($Tabla);


	//print_r(actividadesconsulta($ID,$seleccion));
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

	<title>UNmapa - Gestión de usuarios</title>
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
		#L_valorAct, #L_valorAct, #L_valorDat, #L_valorUni, #L_textoAct, #L_textoDat, #L_categAct, #L_categDat, #L_categLib{
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
		select{
			margin:0;
		}	
	</style>
	
	
</head>

<body>
	
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
		
		echo  "<h1>Gestión de Usuarios:  <span class='menor'>de actividad Nº $Actividad : ".$Contenido['resumen']."</span></h1>";
		
			if($Coordinacion=='activa'){
				echo "<a href='./actividad.php?actividad=$ID'>acceder a la Actividad</a><br>";
				echo "<a href='./actividad_config.php?actividad=$ID'>acceder a la Configuración de la actividad</a>";				
			}
		?>


		<?php
			// formulario para agregar una nueva actividad		
		if($ID==''){
				echo "la actividad no fue llamada correctamnte";
		}else{
			// formulario para modificar una actividad 
			
			echo "<h2>Usuarios Registrados</h2>";
			
			
			foreach($Roles as $rid => $rdata){
				
				echo "<h3>rol: ".$rdata['nombre']."</h3>";
				echo "<table>";
				foreach($Contenido['Acc'][$rid] as $e){
					if($e['usuario']['id']==''){continue;}
					if($rid<3&&$rid<$UsuarioAcc){
						$boton="
						<form method='post' action='./cambia.php' target='ventanaaccion'>
						<input type='hidden' name='tabla' value='cambia'>
						<input type='hidden' name='salida' value='reload'>
						<input type='hidden' name='id' value='".$e['id']."'>
						<input type='hidden' name='tabla' value='ACTaccesos'>
						<select name='nivel' onChange='this.parentNode.submit();'>
						";
						foreach($Roles as $rrd => $rrv){
							if($rrd>=$UsuarioAcc){continue;}
							if($rrd==$rid){$selec='selected';}else{$selec='';}
							$boton.="<option $selec value='".$rrd."'>".$rrv['nombre']."</option>";
						}
						$boton.="
						<select>
						</form>
						";
					}
					
					$u=$e['usuario'];
					echo "<tr>";
					echo "<td>".$u['nombre']."</td>";
					echo "<td>".$u['apellido']."</td>";
					echo "<td>".$u['mail']."</td>";
					echo "<td>$boton</td>";
					echo "</tr>";
					
				}		
				echo "</table>";
				
			}
			
			
			//echo "<pre>";print_r($Contenido['acc']);echo "</pre>";
			
			
			/*

			foreach($Contenido['acc']['editores'] as $e){
				$u=$e['usuario'];
				echo "<tr>";
				echo "<td>".$u['nombre']."</td>";
				echo "<td>".$u['apellido']."</td>";
				echo "<td>".$u['mail']."</td>";
				echo "</tr>";
				
			}
			echo "</table>";
			echo "<h3>rol: participante</h3>";
			echo "<table>";
			foreach($Contenido['acc']['participantes'] as $e){
				$u=$e['usuario'];
				echo "<tr>";
				echo "<td>".$u['nombre']."</td>";
				echo "<td>".$u['apellido']."</td>";
				echo "<td>".$u['mail']."</td>";
				echo "</tr>";
			}
			echo "</table>";
			*/
			echo "<form method='post' target='ventanaaccion' action='./acci_procearusuarios.php?actividad=$ID'>";
			
			echo "<h2> Gestión de usuarios</h2>";
			echo "<h3>genenerar usuarios desde tabla</h3>";
			echo "<p> pegue a continuación su tabla (cortar pegar desde hoja de calculo <a download='download' href='./auxiliar/tabla_ejemplo_usuarios.xls'>ver ejemplo</a>)</p>";
			echo "<textarea name='tablausuarios'></textarea>";
			echo "<input type='submit'>";
			echo "<form>";
			
			echo "<h3>procesar tabla</h3>";
			echo "<div id='tablaproce'>";
			echo "<p>no se han cargado datos de tabla</p>";
			echo "</div>";
			
		}
		?>
	
	</div></div>
	
	<script type='text/javascript'>
		
		function validarContenido(_this){
			if(_this.value=='-agregar nuevo-'){
				_this.setAttribute('value','');
			}
		}
		
	
	</script>
	
	<script type='text/javascript'>
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
		<input type='hidden'  id='InModTab'  name='tabla' value=''>		
		<input type='hidden'  id='InModDat'  name='' value=''>				
		<input type='hidden'  id='InModAct'  name='id_p_actividades_id' value='<?php echo $ID;?>'>
		<input type='hidden'  id='InModTabHtml'  name='TablaHtml' value='<?php echo $ID;?>'>
	</form>
	
	        <script type="text/javascript">        
                    window.scrollTo(0,'<?php echo $_GET['y'];?>');     
        </script>
        
	<?php
include('./includes/pie.php');

?>
</body>
