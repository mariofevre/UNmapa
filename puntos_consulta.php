<?php 
/**
* puntos_consulta.php
*
* genera un json contenioendo los datos de todos los puntos de una actividad
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


ini_set('display_errors',true);

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

$Log=array();
$Log['data']=array();
$Log['tx']=array();
$Log['res']='';

function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res="Error en la codificacion de caracteres ".print_r($Log,true);}
	echo $res;
	exit;
}

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}

$HOY=date("Y-m-d");

if(!isset($_GET['aid'])){
	$Log['tx'][]='error, falta variable aid (actividad id)';
	$Log['res']='err';
	terminar($Log);
}
	
$ID=$_GET['aid'];

//echo json_encode($Res);
$Res['features']=array();

$query="	
	SELECT 
		`ACTaccesos`.`id`,
	    `ACTaccesos`.`id_actividades`,
	    `ACTaccesos`.`id_usuarios`,
	    `ACTaccesos`.`nivel`,
	    `ACTaccesos`.`autorizado`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTaccesos`
	WHERE
		id_actividades='".$ID."'
		AND
		id_usuarios='".$UsuarioI."'
";
$Consulta = $Conec1->query($query);

if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}

$Act['docente']='0';
while($fila= $Consulta->fetch_assoc()){
	$Log['tx'][]=$fila['nivel'];
	$Log['tx'][]=$Actividad['zz_AUTOUSUARIOCREAC'];
	$Log['tx'][]=$UsuarioI;
	$Log['tx'][]='.';	
	if($fila['nivel']>=3){
		$Act['docente']='1';
	}
}	
	


$Res['type']="FeatureCollection";

$query="
	SELECT 
	    `actividades`.`abierta`,
	    `actividades`.`resumen`,
	    `actividades`.`consigna`,
	    `actividades`.`marco`,
	    `actividades`.`nivel`,
	    `actividades`.`x0`,
	    `actividades`.`y0`,
	    `actividades`.`xF`,
	    `actividades`.`yF`,
	    `actividades`.`imx0`,
	    `actividades`.`imy0`,
	    `actividades`.`imxF`,
	    `actividades`.`imyF`,
	    `actividades`.`geometria`,
	    `actividades`.`adjuntosAct`,
	    `actividades`.`adjuntosDat`,
	    `actividades`.`adjuntosExt`,
	    `actividades`.`valorAct`,
	    `actividades`.`valorDat`,
	    `actividades`.`valorUni`,
	    `actividades`.`textobreveAct`,
	    `actividades`.`textobreveDat`,
	    `actividades`.`categAct`,
	    `actividades`.`categDat`,
	    `actividades`.`categLib`,
	    `actividades`.`textoAct`,
	    `actividades`.`textoDat`,
	    `actividades`.`objeto`,
	    `actividades`.`desde`,
	    `actividades`.`hasta`,
	    `actividades`.`resultados`,
	    `actividades`.`zz_AUTOUSUARIOCREAC`,
	    `actividades`.`zz_borrada`,
	    `actividades`.`zz_AUTOFECHACREACION`,
	    `actividades`.`zz_PUBLICO`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
	WHERE
		id='".$ID."'		
	
";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}
	
	
	//echo $query;
	while($fila=$Consulta->fetch_assoc()){
		if($fila['zz_AUTOUSUARIOCREAC']==$UsuarioI){
			$Act['docente']='1';
		}
		
		foreach($fila as $k => $v){
			$Act[$k]=utf8_encode($v);
		}
	}

	
	$query="
		SELECT
			`usuarios`.`id`,
		    `usuarios`.`nombre`,
		    `usuarios`.`apellido`
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
	";
	
	$ConsultaU = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al consultar base';
		terminar($log);
	}
	
	//echo $query;
	while($fila=$ConsultaU->fetch_assoc()){
		$Usu[$fila['id']]=$fila;
	}

	$query="
		SELECT
			`ACTcategorias`.`id`,
		    `ACTcategorias`.`id_p_actividades_id`,
		    `ACTcategorias`.`nombre`,
		    `ACTcategorias`.`descripcion`,
		    `ACTcategorias`.`orden`,
		    `ACTcategorias`.`zz_fusionadaa`,
		    CO_color
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
		WHERE
			zz_borrada='0'
			AND
			id_p_actividades_id='".$ID."'		
		ORDER BY 
			`ACTcategorias`.`orden` ASC
	";
	$ConsultaACTclases = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al consultar base';
		terminar($log);
	}
	
	//echo $query;
	while($fila=$ConsultaACTclases->fetch_assoc()){
		$ActCat[$fila['id_p_actividades_id']][$fila['id']]=$fila;
		// cambia de categoría aquellos puntos dentro de categorías que fueron colapsadas
		if($fila['zz_fusionadaa']>0){
			$dest=$fila['zz_fusionadaa'];
		}else{
			$dest=$fila['id'];
		}		
		$CatConversor[$fila['id']]=$dest;
	}

	$query="
		SELECT `atributos`.`id`,
		    `atributos`.`valor`,
		    `atributos`.`categoria`,
		    `atributos`.`texto`,
		    `atributos`.`textobreve`, 
		    `atributos`.`link`,
		    `atributos`.`id_usuarios`,
		    `atributos`.`id_actividades`,
		    `atributos`.`fecha`,
		    `atributos`.`escala`,
		    `atributos`.`nivelUsuario`,
		    `atributos`.`areaUsuario`
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`atributos`
		WHERE
			id_actividades='".$ID."'
	";
	
	$ConsultaATT = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al consultar base';
		terminar($log);
	}


	while($fila=$ConsultaATT->fetch_assoc()){
		
		// cambia de categoría aquellos puntos dentro de categorías que fueron colapsadas
		if(!isset($CatConversor[$fila['categoria']])){$CatConversor[$fila['categoria']]='SD';}
		$cat=$CatConversor[$fila['categoria']];
		
		$f=$fila;
		$f['categoria']=$cat;
		
		if(isset($ActCat[$ID][$cat])){
			$f['categoriaNom']=$ActCat[$ID][$cat]['nombre'];
			$f['categoriaDes']=$ActCat[$ID][$cat]['descripcion'];
		}else{
			$f['categoriaNom']='';
			$f['categoriaDes']='';
		}
		
		$ATT[$fila['id']]=$f;
	}

	if (isset($seleccion['zoom']))
		$zoom = $seleccion['zoom'];
	else
		$zoom = 0;
	
	$query="
		SELECT 
			`geodatos`.`id`,
		    `geodatos`.`x`,
		    `geodatos`.`y`,
		    `geodatos`.`z`,
		    `geodatos`.`zz_bloqueado`,
		    `geodatos`.`zz_bloqueadoUsu`,
		    `geodatos`.`zz_bloqueadoTx`,
		    `geodatos`.`geometria`,
		    `geodatos`.`id_usuarios`,
		    `geodatos`.`id_actividades`,
		    `geodatos`.`fecha`,
		    `geodatos`.`xPsMerc`,
		    `geodatos`.`yPsMerc`,
		    `geodatos`.`zResPsMerc`
			
			
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
		WHERE
			zz_borrada='0'
		AND 
			id_usuarios>0
		AND 
			id_actividades='".$ID."'
	";	
	
	$ConsultaACT = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al consultar base';
		terminar($log);
	}

	while($fila= $ConsultaACT->fetch_assoc()){
		
		if(
			$fila['x']>180
			||$fila['y']>90
			||$fila['x']<-180
			||$fila['y']<-90
		){continue;}		
		if(!isset($ATT[$fila['id']])){continue;}
		
		if($Act['docente']!='1'&&$fila['zz_bloqueado']==1&&$fila['id_usuarios']!=$UsuarioI){continue;}
		
		
		unset($f);
		$f['type']="Feature";
		$f['geometry']['type']="Point";
		$f['geometry']['coordinates'][]=floatval($fila['x']);
		$f['geometry']['coordinates'][]=floatval($fila['y']);
		
		
		
		
		
		$f['properties']=$Act;
		foreach($ATT[$fila['id']] as $k => $v){
			$f['properties'][$k]=utf8_encode($v);
		}

		//echo $ATT[$fila['id']]['link'].PHP_EOL;
		//echo substr($ATT[$fila['id']]['link'],0,2).PHP_EOL;
		//$f['properties']['linkloc']=substr($ATT[$fila['id']]['link'],0,2);
		//echo substr($ATT[$fila['id']]['link'],0,2).PHP_EOL;
		if(substr($ATT[$fila['id']]['link'],0,2)=='./'){
			
			$f['properties']['linkloc']='remoto';
			$p=strrpos($ATT[$fila['id']]['link'],'.');
			$e = explode(".",$ATT[$fila['id']]['link']);
			$e = strtolower(end($e));
				
			

			//echo "ingresando: $nombre".PHP_EOL;
			//se trata de un documento local
			$f['properties']['linkloc']='local';
			$imgtVal['jpg']='1';
			$imgtVal['png']='1';
			$imgtVal['tif']='1';
			$imgtVal['bmp']='1';
			$imgtVal['gif']='1';

			
			
			if(!file_exists($ATT[$fila['id']]['link'])){
				
				$f['properties']['linkloc']=utf8_encode('perdido'.$ATT[$fila['id']]['link']);
				
			}elseif(isset($imgtVal[strtolower($e)])){
				// se trata de una imagen
				
				$ident='_th';
				$nn=substr($ATT[$fila['id']]['link'], 0, $p).$ident.'.'.$e;
				
				if(!file_exists($nn)){
					
					$i = getimagesize($ATT[$fila['id']]['link']);
					$f['properties']['linkloc2']=$i;
					$ladomayor=100;
					if($i[0]>$ladomayor*1.3&&$i[1]>$ladomayor*1.3){
						escalarImagen($ATT[$fila['id']]['link'], $ladomayor, $ident);
						$f['properties']['linkth']=$nn;
					}else{
						$f['properties']['linkth']=$ATT[$fila['id']]['link'];
					}
				}else{
					$f['properties']['linkth']=$nn;
					
				}
				
				/*$ident='_th';
				$nn=substr($ATT[$fila['id']]['link'], 0, $p).$ident.'.'.$e;
				
				if(!file_exists($nn)){
					$i = getimagesize($nn);
					$ladomayor=1000;
					if($i[0]>$ladomayor*1.3&&$i[1]>$ladomayor*1.3){
						escalarImagen($ATT[$fila['id']]['link'], $ladomayor, $ident);
					}
				}*/
			}
		}elseif(substr($ATT[$fila['id']]['link'],0,5)=='http:'){
			$f['properties']['linkloc']='remoto';
		}
			
		foreach($fila as $k => $v){
			$f['properties'][$k]=utf8_encode($v);
		}
		
		if(isset($ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'])){			
			$f['properties']['style']["color"]=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'];
		}else{			
			$f['properties']['style']["color"]="rgb(80,120,250)";
		}

		    
		$f['properties']['style']['fill']='red';
		$f['properties']['style']['stroke-width']='3';
		$f['properties']['style']['fill-opacity']=0.6;
		
		if(isset($Usu[$fila['id_usuarios']])){
			$f['properties']['nombreUsuario']=utf8_encode($Usu[$fila['id_usuarios']]['nombre']." ".$Usu[$fila['id_usuarios']]['apellido']);
		}
    	$f['properties']['sel']='no';
		$Res['features'][]=$f;
		
		//$Res['features']=array_slice ( $Res['features'], 0 , 50);
		
	}

	terminar($Res);


										
function escalarImagen($localizacion, $ladomayor, $ident) {

	$filterType=imagick::FILTER_CUBIC;
	$blur=1;
	$bestFit=0;
	$cropZoom=0;
	
	$p=strrpos($localizacion,'.');
	
	$e = explode(".",$localizacion);
	$e = end($e);
	
	$nn=substr($localizacion, 0, $p).$ident.'.'.$e;
	
	copy($localizacion, $nn);
	chmod($rut,0777);
	if($nn==''){return;}				
    $imagick = new \Imagick(realpath($nn));
	
	$ancho = $imagick->getImageWidth();
	$alto = $imagick->getImageHeight();	
	if($ancho>($ladomayor*1.3)||$alto>($ladomayor*1.3)){
		if($ancho>$alto){			
			$e=$ladomayor/$ancho;
		}else{
			$e=$ladomayor/$alto;
		}
		$nalto=round($alto*$e);		
		$nancho=round($ancho*$e);

	    $imagick->resizeImage($nancho, $nalto, $filterType, $blur, $bestFit);
	    $imagick->writeImage ($nn);
		chmod($nn,0777);
		$Log['tx'][] ="archivo resamplead e:$e ($ancho x $alto > $nancho x $nalto)";
	}
}


?>