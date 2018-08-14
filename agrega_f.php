<?php
// eliminar esta funcion solo se utiliza para formuladrio de edición de categorías, pero es muy insegura
/**
* agrega_f.php
*
* aplicación para la generación de formularios para modificar el contenido de una base de datos enviando vía POST instrucciones a aplicaciones de edicion.
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010-2015 TReCC SA
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


/* verificación de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];

if($UsuarioI==""){header('Location: ./login.php');}

	
	$Id_contrato = $_GET["contrato"];
	$Tabla = $_GET["tabla"];
	$Id = $_GET["id"];
	$Accion = $_GET["accion"];
	$Modo = $_GET["modo"];
	
	$CampoS = $_GET["campos"];	
	if($Campo==''&&$CampoS!=''){$Campo="--HHHHH--";}
	
	$Tablahermana = $_GET["tablahermana"];
	$Idhermana = $_GET["idhermana"];
	
	$campofijo = $_GET["campofijo"];
	if($campofijo == ""){$campofijo = "vacio";}
	$campofijo_c = $_GET["campofijo_c"];
	
	$campofijob = $_GET["campofijob"];
	if($campofijob == ""){$campofijob = "vacio";}
	$campofijob_c = $_GET["campofijob_c"];

	$Salida = $_GET["salida"];
	$Salida =  $_GET['salida'];
	$Salidaid = $_GET["salidaid"];	
	$Salidatabla = $_GET["salidatabla"];
												
	
	
?>
<!DOCTYPE html>
<head>
	<title>Panel de Control</title>
	<link rel="stylesheet" type="text/css" href="./css/general.css">
	<?php
	if($Modo!='mini'){
		echo '<link rel="stylesheet" type="text/css" href="./css/panelbase.css">';
	?>
	<style type="text/css">

		.gris {
		background-color:lightgray;
		}
		
		.carga {
		background-color:#A9F5BC;
		}
		
		select{
		width:300px;
		float:left;
		}
		
		form{
		margin:0;
		width:160px;
		width:auto;
		}

		table{
		width:800px;
		margin:5px;
		}

		textarea{
			font-family:'arial';
			font-size:11px;
			width:100%;
		}
		
		.dato{
		min-height: 55px;
		border-top:2px solid #000;
		margin-top:2px;
		}
		
		.boton{
		width:80px;		
		}
		
				
		.boton2{
		width:25px;		
		}
		
		.boton3{
		width:150px;
		}
		
		p{
		margin:0 10px;
		}
		
		h1{
		margin:0 10px;
		}
		
		input.fecha{
		float:left;
		width:40px;		
		}		

		input.chico{
		width:300px;
		}		
		
		input.mini{
		float:left;
		width:25px;
		}		
		
		select.chico{
		width:100px;
		}			
			
		th{
		font-size:12px;
		text-align:right;
		background-color:lightblue;
		width:100px;
		}

		.paquete{
		width:300px;
		margin-bottom:5px;
		float:left;
		border: 1px gray solid; 
		padding: 5px;
		background-color:#C4DBE2;
		}
				
		.salva{
		width:90px;
		margin-bottom:5px;
		float:left;
		}

		.reporte{
		width:200px;
		}

		.marco{
		border-bottom: 1px solid gray;
		float: none;
		width: 100%;		
		}
		
		.referencia{
		font-size: 10px;
		}

		.alerta{
		background-color:red;
		}

		.similth{
		background-color:none;
		width:800px;
		text-align:left;
		}	
	
	
		.similth > div{
		background-color:lightblue;
		width:100px;
		margin-left:3px;
		border:white 2px solid;
		text-align:right;
		position:relative;
		top:-4px;
		}
		
	#reporte_bid{
	float:left;
	}
	
	#PTUBA{
	float:left;
	}
	
	#imagenesI{
	float:left;
	}
	
	#imagenesII{
	float:left;
	}
	
	iframe{
		border:1px solid gray;
		width:310px;
		height:80px;
	}

	input[readonly='readonly']{	
		background-color:lightblue;		
		height:4px;
	}

	input[type="button"]{
	    height:19px;
	    min-width:60px;
	    position:absolute;
	}
	input[type="submit"]{
	    height:19px;
	    min-width:60px;
	    position:absolute;	  
	    top:20px;
	    left:150px;
	    width:105px;	      
	}
	input[type="reset"]{
	    height:19px;
	    min-width:60px;
	    position:absolute;		  
	    top:20px;
	    left:260px;      
	    width:105px;	    
	}		

	input[value="Eliminar"]{
	    height:19px;
	    min-width:60px;
	    position:absolute;		  
	    top:20px;
	    left:490px;    
	    width:105px;
	    background-color:#FA5858;
	    font-weight:bold;
	    color:#000;
	}		

	input[value="Cancelar"]{
	    height:19px;
	    min-width:60px;
	    position:absolute;		  
	    top:20px;
	    left:370px;    
	    width:105px;
	}		


	
	.dato > a{
	    display: inline-block;
	    height: 12px;
	    overflow: visible;
	    width: 145px;
	}
	
	.dato > a:hover{
	    z-index:30;
	}	
	
	.contenedor{
		font-size:9px;
		height:10px;
		display:inline-block;
		width:20px;
		margin-right:2px;
		position:relative;
		overflow:visible;
		vertical-align: top;
	}
	.contenido{
		background-color:lightblue;
		border:#08AFD9 1px solid;
		color:#000;
		display:inline-block;
		width:19px;
		height:10px;
		margin:0px;
		position:absolute;
		overflow:hidden;
		text-align:center;
	}
	
	.subcontenedor.oculto{
		display:none;
		width:90px;
		height:auto;
		position:absolute;
	}
	
	
	
	.contenedor:hover > .contenido.aclara{
		display:none;
	}
	
	.contenedor:hover > .subcontenedor.oculto{
		display:block;
	}
	
	.contenedor:hover > .subcontenedor.oculto > .contenido.oculto{
		display:block;
		position:absolute;		
		width:auto;
		min-width:20px;
		max-width:90px;
		z-index:20;
		height:auto;
		border:1px solid #000;
	}	
	
			.muestracolor{
			display:inline-block;
			border:1px solid gray;
			width:50px;
			height:17px;
			vertical-align: middle;
			margin:2px;
		}
		#hoja{
			position:relative;
			width:100%;
			padding-top:40px;
		}
	</style>
	<?php
	}else{
		echo '<link rel="stylesheet" type="text/css" href="./css/mini.css">';
	}
?>
	
	
	<script LANGUAGE="javascript">
	_fechaobjetivo='';
	</script>	
	
</head>
<body>
	<?php
	
	if($Modo!='mini'){
		//echo "hola $UsuarioN <br>";
		//echo "has accedido como $UsuarioAcc <br>";
		if($GrupoAcc ==''){
			//echo "del panel general <br>";
		}else{
			//echo "del grupo $GrupoAcc ";
		}
		if($GrupoAcc =='' && $UsuarioAcc=='administrador'){
			$edicionextra = 'si';
			echo "dados sus permisos se habilita en esta página la edición de listados asociados.<br>";	
		}
	}
	
	


	if($Accion == ""){
		$Accion = "agrega";
	}		
	
	$query="SELECT * FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.$Tabla WHERE id='$Id'";
	$Consulta = $Conec1->query($query);
	$Consulta_filas = $Consulta->num_rows;
	$Registro=$Consulta->fetch_assoc();
	
	/*print_r($config);*/

		$Datos = "";
	
	if ($Accion == "agrega"){$Href = "agrega.php";$AccionNom = "Agregar";}
	elseif ($Accion == "cambia"){$Href = "cambia.php";$AccionNom = "Guardar";}
	elseif ($Accion == "borra"){$Href = "borra.php";$AccionNom = "Borrar";}
	else {$Href = "error.php";}
	
	if ($Accion == "agrega" || $Accion == "cambia"){
	?>
	<script type="text/javascript">
	function intepretarcolor(_Cstring){		
		return _Cstring;
	}	

	function cargaColorValue(_this){
		_muestra=_this.nextSibling;
		_contenido=_this.value;
		_colorVal=intepretarcolor(_contenido);
		_muestra.style.backgroundColor=_colorVal;
		
	}
	function cargaColor(_id,_contenido){
		_muestra=document.getElementById(_id);
		_colorVal=intepretarcolor(_contenido);		
		_muestra.style.backgroundColor=_colorVal;		
	}	
</script>

			<div id="marco">
				<form action="./<?php echo $Href;?>" method="POST" enctype='multipart/form-data'>
					<input type="hidden" name="tabla" value="<?php echo $Tabla;?>">
					<input type="hidden" name="contrato" value="<?php echo $Id_contrato;?>">			
					<input type="hidden" name="id" value="<?php echo $Id;?>">
					<input type="hidden" name="<?php echo $campofijo;?>" value="<?php echo $campofijo_c;?>">
					<input type="hidden" name="<?php echo $campofijob;?>" value="<?php echo $campofijob_c;?>">					
					<input type="hidden" name="accion" value="<?php echo $Accion;?>">
					<input type="hidden" name="campo" value="<?php echo $Campo;?>">
					<input type="hidden" name="tablahermana" value="<?php echo $Tablahermana;?>">
					<input type="hidden" name="idhermana" value="<?php echo $Idhermana;?>">	
					<input type="hidden" name="salida" value="<?php echo $Salida;?>">
					<input type="hidden" name="salidaid" value="<?php echo $Salidaid;?>">					
					<input type="hidden" name="salidatabla" value="<?php echo $Salidatabla;?>">										
	
					<h1>Formulario de Edición de Categorías</h1>
					<div id="hoja">	
			
						<?php	
						$query='SHOW FULL COLUMNS FROM `'.$_SESSION['Unmapa'][$CU]->DATABASE_NAME.'`.`'.$Tabla.'`';
					    $Consulta = $Conec1->query($query);
					    if ($Consulta->num_rows > 0) {
					    			 
							$empaquetado = -100;
							$borradato="no";
					        while ($row = $Consulta->fetch_assoc()) {
					        	
								foreach($CampoS as $c){
									
									if($c==$row['Field']){										
										$campohabilitado='si';	
									}
								}
					        	
								if($Tabla=='ACTcategorias'&&$row['Field']=='id_p_actividades_id'){continue;}
								
								if($row['Default']!=''&&$row['Default']!=null){$Def=$row['Default'];}else{$Def='';}
								
					        	$wheremas ="";
					        	
								$comentario = $row['Comment'];
								
									
								
								if($empaquetado == -100){echo "<div class='paquete'>";$empaquetado =0;}
								
								if($borradato=="no"){
									echo "<div class='dato'>";
								}else{
									$borradato="no";
								}
								
								if($Accion != "agrega"){
									$contenido = $Registro[$row['Field']];
								}elseif($Accion == "agrega"){
									$x = "C-".$row['Field'];
									$contenido = $_GET[$x];
									if($contenido==''){
										$contenido = $Def;
									}
								}
			
								$i=substr($row['Field'],0,3);
								/* print_r($row); */
								$Type = substr($row['Type'],0,3);
								
								if($row['Field'] == 'id'){
									$empaquetado --;
									$borradato="si";
								
								}elseif($row['Field'] == 'id_p_B_usuarios_usuarios_id_nombre'){
									echo '<input type="hidden" name="id_p_B_usuarios_usuarios_id_nombre" value="'.$UsuarioI.'">';
									echo "<p>".$comentario.": ".$UsuarioN."</p>";
								}elseif($i == 'zz_'){
									$valor='';
									
									if($row['Field']=="zz_pass"){
										echo $comentario;
										echo "<input type='password' name='".$row['Field']."' value='".$valor."'>";
										
									}else{
										
										echo "<input type='hidden' name='".$row['Field']."' value='".$valor."'>";
										$empaquetado --;
										$borradato="si";
									}
									$valor='';
								
									
								}elseif($row['Field'] == $campofijo){
									echo "<p>".$comentario.": ".$campofijo_c."</p>";

								}elseif($row['Field'] == $campofijob){
									echo "<p>".$comentariob.": ".$campofijob_c."</p>";
								
								}elseif($i == 'FI_'){
									if($Campo == "" || $Campo == $row['Field'] || $campohabilitado=='si'){
										echo "<p>".$comentario.": ".$campofijo_c."</p>";
										if($contenido!=''){
											echo "<a href='$contenido'>documento actual</a>";
											echo "reemplazar por:";
										}	
										echo "<input type='hidden' name='".$row['Field']."' value='".$contenido."'>";
										
										$path='path'.$row['Field'];
										echo "<input type='hidden' name='archivo_".$row['Field']."_path' value='".$_GET[$path]."'>";	
										
										echo "<input type='file' name='archivo_".$row['Field']."'>";
									}else{
										echo "<input type='hidden' name='archivo_".$row['Field']."' value='".$contenido."'>";
									}
								}elseif($i == 'CO_'){
									if($Campo == "" || $Campo == $row['Field']||$campohabilitado=='si'){									
										echo "<p>".$comentario."</p>";
										
										echo "<input ".$row['habilitado']." type='text' name='".$row['Field']."' value='".$contenido."' onkeyup='cargaColorValue(this);'><div id='MuCol".$row['Field']."' class='muestracolor'></div>";
										
										
										if($contenido!=''){
											
											echo "<script type='text/javascript'>
											cargaColor('MuCol".$row['Field']."','".$contenido."');
											</script>";				
										}									
									}
								}elseif($i != 'id_'){
									if($Campo == "" || $Campo == $row['Field']||$campohabilitado=='si'){
										echo $comentario;
										echo "<br>";
										
										if ($Type == "tex"){
											$empaquetado =+ 3;
											?>
											<textarea cols="34" rows="8" name="<?php echo $row['Field'];?>"><?php echo $contenido;?></textarea>
											
											<?php	
										}elseif ($Type == "dat"){
											/* if($Accion == "agrega" ){$contenido = date("Y") . "-" . date("m") . "-" . date("d");} */
											if($Accion == "agrega" && $row['Field'] == "fechacierre"){$contenido = "0000-00-00";}
											if($Accion == "agrega" && $Tabla == "comunicaciones" && $row['Field'] == "cerradodesde"){$contenido = "0000-00-00";}
											if($row['Comment'] == "fecha en que se realiza el pedido"){$estado = "READONLY";}else{$estado = "";}
											?>
											<input class="mini" type="text" size="2" id="<?php echo $row['Field'] . "_d";?>" name="<?php echo $row['Field'] . "_d";?>" value="<?php echo substr($contenido,8,2);?>" <?php echo $estado;?>
												<?php 
													if($Accion == "agrega" && $Tabla == "comunicaciones" && $row['Field'] == "fecharecepcion"){
														?>
														onchange="
															getElementById('fechainicio_d').value = this.value;
															if(_fechaobjetivo=='fijada'){
																getElementById('fechaobjetivo_d').value = this.value;
																getElementById('cerradodesde_d').value = this.value;
															}
														"
														<?php
													}
												?>
											
											>
											<input class="mini" type="text" size="2" id="<?php echo $row['Field'] . "_m";?>" name="<?php echo $row['Field'] . "_m";?>" value="<?php echo substr($contenido,5,2);?>" <?php echo $estado;?>
												<?php 
													if($Accion == "agrega" && $Tabla == "comunicaciones" && $row['Field'] == "fecharecepcion"){
														?>
														onchange="
															getElementById('fechainicio_m').value = this.value;
															if(_fechaobjetivo=='fijada'){
																getElementById('fechaobjetivo_m').value = this.value;
																getElementById('cerradodesde_m').value = this.value;
															}
														"
														<?php
													}
												?>
											>
											<input class="fecha" type="text" size="2" id="<?php echo $row['Field'] . "_a";?>" name="<?php echo $row['Field'] . "_a";?>" value="<?php echo substr($contenido,0,4);?>" <?php echo $estado;?>
												<?php 
												
													if($Accion == "agrega" && $Tabla == "comunicaciones" && $row['Field'] == "fecharecepcion"){
														?>
														onchange="
															getElementById('fechainicio_a').value = this.value;
															if(_fechaobjetivo=='fijada'){
																getElementById('fechaobjetivo_a').value = this.value;
																getElementById('cerradodesde_a').value = this.value;
															}
														"
														<?php
													}
											echo ">";	
											if($contenido!=''){
												?>
												<input type='button' value='borrar fecha' 
													onclick="
														document.getElementById('<?php echo $row['Field'];?>_d').value = '00';
														document.getElementById('<?php echo $row['Field'];?>_d').setAttribute('readonly', 'readonly');
														document.getElementById('<?php echo $row['Field'];?>_m').value = '00';
														document.getElementById('<?php echo $row['Field'];?>_m').setAttribute('readonly', 'readonly');
														document.getElementById('<?php echo $row['Field'];?>_a').value = '0000';
														document.getElementById('<?php echo $row['Field'];?>_a').setAttribute('readonly', 'readonly');
														">
												<?php
											}
											
											?>
											
											
											
											<?php
										}elseif ($Type == "enu"){
											$campo=$row['Field'];
											$listado = $row['Type'];
											$listado = str_replace("enum('","",$listado);
											$listado = str_replace("')","",$listado);
											$lista = explode("','", $listado);
											echo "<select name='".$campo."' ";
												
												if($Accion == "agrega" && $Tabla == "comunicaciones" && $row['Field'] == "requerimiento"){
													?>
													onchange="
														if(this.value=='no'){
															_fechaobjetivo='fijada';
															getElementById('fechaobjetivo_a').value = getElementById('fecharecepcion_a').value;
															getElementById('fechaobjetivo_m').value = getElementById('fecharecepcion_m').value;	
															getElementById('fechaobjetivo_d').value = getElementById('fecharecepcion_d').value;	
															
															getElementById('cerradodesde_a').value = getElementById('fecharecepcion_a').value;
															getElementById('cerradodesde_m').value = getElementById('fecharecepcion_m').value;
															getElementById('cerradodesde_d').value = getElementById('fecharecepcion_d').value;
															
															getElementById('cerradodesde_a').setAttribute('readOnly','readonly');
															getElementById('cerradodesde_a').style.backgroundColor='lightblue';
															getElementById('cerradodesde_m').setAttribute('readOnly','readonly');
															getElementById('cerradodesde_m').style.backgroundColor='lightblue';
															getElementById('cerradodesde_d').setAttribute('readOnly','readonly');
															getElementById('cerradodesde_d').style.backgroundColor='lightblue';
															
															getElementById('fechaobjetivo_a').setAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_a').style.backgroundColor='lightblue';
															getElementById('fechaobjetivo_m').setAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_m').style.backgroundColor='lightblue';
															getElementById('fechaobjetivo_d').setAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_d').style.backgroundColor='lightblue';		
															
															getElementById('fechainicio_a').setAttribute('readOnly','readonly');
															getElementById('fechainicio_a').style.backgroundColor='lightblue';
															getElementById('fechainicio_m').setAttribute('readOnly','readonly');
															getElementById('fechainicio_m').style.backgroundColor='lightblue';
															getElementById('fechainicio_d').setAttribute('readOnly','readonly');
															getElementById('fechainicio_d').style.backgroundColor='lightblue';															
														};
														if(this.value=='si'){
															this.form.cerradodesde_a.removeAttribute('readOnly'); 
															
															getElementById('cerradodesde_a').removeAttribute('readOnly','readonly');
															getElementById('cerradodesde_a').style.backgroundColor='white';
															getElementById('cerradodesde_m').removeAttribute('readOnly','readonly');
															getElementById('cerradodesde_m').style.backgroundColor='white';
															getElementById('cerradodesde_d').removeAttribute('readOnly','readonly');
															getElementById('cerradodesde_d').style.backgroundColor='white';
															
															getElementById('fechaobjetivo_a').removeAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_a').style.backgroundColor='white';
															getElementById('fechaobjetivo_m').removeAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_m').style.backgroundColor='white';
															getElementById('fechaobjetivo_d').removeAttribute('readOnly','readonly');
															getElementById('fechaobjetivo_d').style.backgroundColor='white';		
															
															getElementById('fechainicio_a').removeAttribute('readOnly','readonly');
															getElementById('fechainicio_a').style.backgroundColor='white';
															getElementById('fechainicio_m').removeAttribute('readOnly','readonly');
															getElementById('fechainicio_m').style.backgroundColor='white';
															getElementById('fechainicio_d').removeAttribute('readOnly','readonly');
															getElementById('fechainicio_d').style.backgroundColor='white';		
														}
													
													"
													<?php
												}
											
											echo ">";
											foreach ($lista as $v) {	
												if($contenido == $v){$selecta = "selected='yes'";
												}else{$selecta = "";}
												if($v == 'entrante'){$va = $entra." (".$v.")";
												}elseif($v == 'saliente'){$va = $sale." (".$v.")";
												}else{$va = $v;}
											    echo "<option value='" . $v . "'" . $selecta . ">". $va . "</option>";
											}
											echo "</select>";									
											
											?>	
											 
											<?php
										}elseif ($row['Type'] == "tinyint(1)"){
											$campo=$row['Field'];
											if($contenido==1){$contenidocheck=" checked";}else{$contenidocheck="";}
											?>	
											
											<input type="hidden" name="<?php echo $row['Field'];?>" id="<?php echo $row['Field'];?>" value="">
											<input type="checkbox" name="" value="" <?php echo $contenidocheck;?> 
											 onclick="
											 	alterna('<?php echo $row['Field'];?>', this.checked);
											 	<?php if(($campo=='com-aprobacion'||$campo=='com-aprobacion-sale')&&$Tabla=='configuracion'){
											 		echo "
											 			window.open('./ediciontablagenerica.php?tabla=comunestadoslista&accion=cambia','mywindow');
											 		";
												}?>
											"
											>
											<?php 
												if(
													($campo=='com-aprobacion'||$campo=='com-aprobacion-sale')
													&&$Tabla=='configuracion'&&$contenido=='1'
												){
											 		echo "
											 			<a target='_blank' href='./ediciontablagenerica.php?tabla=comunestadoslista&accion=cambia'>
											 			ver listado de aprobaciones
											 			</a>
											 		";
												}elseif($campo=='doc-visadomultiple'&&$Tabla=='configuracion'&&$contenido=='1'){
											 		echo "
											 			<a target='_blank' href='./ediciontablagenerica.php?tabla=DOCvisados&accion=cambia'>
											 			ver listado de aprobaciones
											 			</a>
											 		";
												}
											?>
											
																						
											
											<?php
										}else{
											?>
											<input id="<?php echo $row['Field'];?>" class="chico" type="text" size="2" name="<?php echo $row['Field'];?>" value="<?php echo $contenido;?>">
											
											<?php
											
										}
										
									}elseif($campofijo == $row['Field']){
									?>	
									<input type="hidden" name="<?php echo $campofijo;?>" value="<?php echo $campofijo_c;?>">
									<?php
										
									}else{
										?>
										<input class="chico" type="hidden" size="2" name="<?php echo $row['Field'];?>" value="<?php echo str_replace('"',"'",$contenido);?>">
										<?php				
									}
									
									
								}elseif($i == 'id_' && $row['Field'] != $campofijo && $row['Field'] != "id_p_paneles_id_nombre"){
									
									if($Campo != "" && $Campo != $row['Field']){
										if($campofijo == $row['Field']){
											?>	
											<input type="hidden" name="<?php echo $campofijo;?>" value="<?php echo $campofijo_c;?>">
											<?php
										}else{
											?>
											<input class="chico" type="hidden" size="2" name="<?php echo $row['Field'];?>" value="<?php echo str_replace('"',"'",$contenido);?>">
											<?php	
										}
										
									}else{
										
										$Typolink = substr($row['Field'],0,4);
										/* para tablas padre */
										if($Typolink == "id_p" && ($config['com-grupob']!="NO" || $row['Field']!="id_p_grupos_id_nombre_tipob")){	
											$Baselink = substr($row['Field'],0,6);
											/* para tablas padre en otras bases*/
											
												
												/* para tablas padre en la misma base*/
												$o = explode("_", $row['Field']);
												$basepadre = $Base;
												$tablapadre = $o[2];
												$padre = $basepadre . "." . $tablapadre;
												echo $comentario;
												
												
												/*echo "<a target='_blank' href='./ediciontablagenerica.php?tabla=".$tablapadre."'> (editar listado)</a>";*/
												
												if($edicionextra =='si'){
													$extracondicion='';
													if($o[5]=='tipo'){$extracondicion.='&campofijo=tipo&campofijo_c='.$o[6];}
													/*
													echo " <a href='./agrega_f.php?tabla=$tablapadre&salida=$Salida&accion=agrega$extracondicion'>agregar ".$tablapadre."</a>";
													 * */
												}
												$query='SHOW FULL COLUMNS FROM '.$basepadre.'.'.$tablapadre;
												$ConsultaEP = $Conec1->query($query);
												$Conec1->error;	
						  						if ($ConsultaEP->num_rows > 0) {
						  							$campozzborra='no';
													$wheremas = "WHERE '1'='1'";
						  							while ($value =$ConsultaEP->fetch_assoc()){
						  								if($value['Field']=='zz_borrada'){
															$wheremas .= " AND zz_borrada='0'";
														}
													}					  					
						  							$ConsultaEP->data_seek(0);
						  							
													while ($value = $ConsultaEP->fetch_assoc()){
														if(substr($value['Field'],0,15)=="id_p_paneles_id"){
															$wheremas .= " AND ".$tablapadre.".".$value['Field']." = ".$PanelI;
														}
													}					  							
						  							
												}		
												if($o[5]!=''){
													if($o[5]=='tipoa'){$wheremas.=" AND $tablapadre.tipo = 'a'";}
													if($o[5]=='tipob'){$wheremas.=" AND $tablapadre.tipo = 'b'";}
													if($o[5]=='tipo'){$wheremas.=" AND $tablapadre.tipo ='".$o[6]."'";}
												}
																								
												if($Tabla == 'comunicaciones' || $Tabla == 'indicadores'){
												$JOIN = "LEFT JOIN $Tabla ON $Tabla.".$row['Field']." = ".$tablapadre.".id";
												$GROUP = "GROUP BY ".$tablapadre.".id";
												$ORDER = "ORDER BY ".$Tabla.".id DESC";
												}
												
												if($tablapadre=='comunicaciones'){
													$ORDER = "ORDER BY ".$tablapadre.".id DESC";
												}
												$query='SHOW FULL COLUMNS FROM '.$padre;
												$ConsultaEP = $Conec1->query($query);
												if ($ConsultaEP->num_rows > 0) {
													if($tablapadre == 'grupos')
														{$wheremas .= " AND ".$tablapadre.".id_p_paneles_id_nombre = '$PanelI' ";
													}elseif($tablapadre == 'paneles'){
														$wheremas .= " AND id = '$PanelI' ";
													}
													
													
													/*echo $JOIN." ".$wheremas;*/
													$query= "SELECT * FROM $padre $JOIN $wheremas $GROUP $ORDER";
													$ConsultaR = $Conec1->query($query);
													echo $Conec1->error;
													$Consultados_filas =$ConsultaR->num_rows;	
													
													echo "<input type='hidden' id='".$row['Field']."' name='".$row['Field']."' value='".$contenido."'>";
													
													
													if($row['Field']=='id_p_comunicaciones_id_ident_entrante'||$row['Field']=='id_p_comunicaciones_id_ident_rechazada'||$row['Field']=='id_p_comunicaciones_id_ident_aprobada'){
														$readonly = "READONLY";
													}else{$readonly = "";}/* algunos campos no permiten la autogeneración de nuevos registros asociados*/
													
													echo "<input $readonly class='chico' id='".$row['Field']."-n' name='".$row['Field']."_n' value=''";
													?>
														 onKeyUp="
														 _valor = this.value;
														 _campo = '<?php echo $row['Field'];?>';
														 document.getElementById('<?php echo $row['Field'];?>').value = includes(_campo, _valor);"												 
													<?php
												
												
													echo"><br>";
													?>
													   <a 
													    onclick="
													    	document.getElementById('<?php echo $row['Field'];?>-n').value = '';
													    	document.getElementById('<?php echo $row['Field'];?>').value = '0'
													    ">
															-vacio-
														</a>
													<?php
													
													
													$campovalor = isset($o[4])?$o[4]:'nombre';
													
													$ocultoporexeso='no';
													$cc=0;
													while($roww = $Consulta->fetch_assoc()){
														$cc++;														
														$v = $row[$campovalor];
														$va = $v;
														
														if($cc>'50'&&$ocultoporexeso=='no'){
															//oculta los resultados posteriores al numero 50 para evitar formularios confusos
															$ocultoporexeso='si';
															echo "<br><a ";?>onclick="this.nextSibling.style.display='block';"<?php echo ">más (más viejos)-></a>";
															echo "<div class='dato' style='display:none;'>";
														}
														
														$vt = $roww['nombre'];
														$v = ($v != '') ? $v : $vt;
														$idv = $roww['id'];
														if($idv==$contenido){
															?>
															<script LANGUAGE="javascript">
																document.getElementById('<?php echo $row['Field'];?>-n').value = '<?php echo $v;?>';
															</script>
															<?php
														}
															?>
															
														<script LANGUAGE="javascript">	
															var <?php echo $row['Field'];?>=new Array();
															<?php echo $row['Field'];?>['id']=new Array();
															<?php echo $row['Field'];?>['n']=new Array();
															
															<?php echo $row['Field'];?>['id'].push("<?php echo $idv;?>");
															<?php echo $row['Field'];?>['n'].push("<?php echo $v;?>");  
		
														</script>	
																
														<?php													
														
														$Consultados_filas_cuenta ++;
													}

													if($ocultoporexeso=='si'){
															//oculta los resultados posteriores al numero 50 para evitar formularios confusos
															echo "</div>";
													}

													if(isset($Opcionespostergadas)){
														echo "<br><a ";?>onclick="this.nextSibling.style.display='block';"<?php echo ">más (de otro tipo)-></a>";
														echo "<div class='dato' style='display:none;'>";
														foreach($Opcionespostergadas as $rowid => $rownombre){
															?>
															 <a 
														    title='<?php echo $rownombre;?>'
															
														   	ondblclick="window.location='./agrega_f.php?accion=cambia&tabla=<?php echo $tablapadre;?>&id=<?php echo $rowid;?>';"												   
														    onclick="
														    	document.getElementById('<?php echo $row['Field'];?>-n').value = '<?php echo $rownombre;?>';
														    	document.getElementById('<?php echo $row['Field'];?>').value = '<?php echo $rowid;?>'
														    ">
																<?php echo $rownombre;?>
															</a>
															<?php
														}
														
														echo "</div>";
														unset($Opcionespostergadas);
													}
													
																	
													
												}
												
										}else{
											$empaquetado --;
											$borradato="si";
										}
									}
								}elseif($row['Field'] == "id_p_paneles_id_nombre"){
									echo "<input type='hidden' name='id_p_paneles_id_nombre' value='".$PanelI."'>";
									$empaquetado --;
									$borradato="si";
								}else{
									$empaquetado --;
									$borradato="si";
								}

								
								/*print_r($row);*/
								if($borradato!="si"){
								echo "</div>";	
								}
								$empaquetado ++;
								if($empaquetado > 5){
									echo "</div>";
									$empaquetado = -100;
								}
					        }
					    }
	
						?>
						
					
						
					</div>	
							
					<input type="submit" value="<?php echo $AccionNom;?>">
					<input type="reset" value="Reiniciar" onclick='window.location=window.location'>	
									
					
					
					<?php
					
					if($Salida=='cerrar'){
						$acc="window.close()";
					}else{
						$acc="window.location.href='./".$Salida.".php?tabla=".$Tabla."&salida=".$Salida."&salidatabla=".$Salidatabla."'";
					}
					
					echo "<input type='button' value='Cancelar' onclick='".$acc."'>";
					
					
					if($Campo==''){
						?>
						<input type="button" value="Eliminar" onclick="window.location.href = './agrega_f.php?tabla=<?php echo $Tabla;?>&id=<?php echo $Id;?>&accion=borra&salida=<?php echo $Salida;?>&salidatabla=<?php echo $Salidatabla;?>';">
						<?php
					}
					?>
				</form>
			</div>


	
	<?php
		}
elseif($Accion == "borra"){
	?>
			<div id="marco">
				<form action="./<?php echo $Href;?>" method="POST">
					<input type="hidden" name="tabla" value="<?php echo $Tabla;?>">
					<input type="hidden" name="contrato" value="<?php echo $Id_contrato;?>">
					<input type="hidden" name="id" value="<?php echo $Id;?>">
					<input type="hidden" name="accion" value="<?php echo $Accion;?>">
					<input type="hidden" name="tablahermana" value="<?php echo $Tablahermana;?>">
					<input type="hidden" name="idhermana" value="<?php echo $Idhermana;?>">
					<input type="hidden" name="salida" value="<?php echo $Salida;?>">
					<input type="hidden" name="salidaid" value="<?php echo $Salidaid;?>">					
					<input type="hidden" name="salidatabla" value="<?php echo $Salidatabla;?>">	
					<h1><?php echo $Accion . " " . $Tabla;?></h1>
					<input type="submit" value="<?php echo $Accion;?>">	
					<div id="hoja">	
			
						<?php	
						$query='SHOW FULL COLUMNS FROM `'.$Tabla.'`';
					    $Consulta = $Conec1->query($query);
					    if ($Consulta->num_rows> 0) {		    	
							
					        while ($roww = $Consulta->fetch_assoc()) {
					 	
					        	$contenido = $row[$roww['Field']];
		
								if($Accion == "agrega"){$contenido = "";}					
								echo '<div class="campo">';	
									$i=substr($row['Field'],0,2);
									echo $row['Comment']. " ".$contenido;
								echo '</div>';	
					        }					        
					    }
					?>
						
					</div>	
									
				</form>
			</div>
	<?php	
	}
	?>	
	


<script type="text/javascript">

	function cambiame() 
{ 
    window.open("","ventanita","width=800,height=600,toolbar=0"); 
    var o = window.setTimeout("document.form1.submit();",500); 
}

	function cambiametb() 
{ 
    window.open("","ventanitatb","width=800,height=600,toolbar=0"); 
    var o = window.setTimeout("document.form1.submit();",500); 
}  

function include(arr, obj) {
    for(var i=0; i<arr['n'].length; i++) {
        if (arr['n'][i] == ob){ return arr['id'][i];}
        else {return 'n';}
    }
}

function includes(_arr, obj) {
    return 'n';
}

function alterna(_id, _estado){
	if(_estado==false){
		document.getElementById(_id).value='0';
	}else if(_estado==true){
		document.getElementById(_id).value='1';
	}
}


</script>
</body>