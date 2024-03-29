<?php 
/**
* actividades_consulta.php
*
* aplicaci�n que consulta el listado de actividades presentadas.
* 
* @package    	Plataforma Colectiva de Informaci�n Territorial: UBATIC2014
* @subpackage 	BASE
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/


/**
* genera listado html de actividades
*
* @param int $ID id de la argumentaci�n. null devuelve la totalidad de actividades cargadas por este usuario o publicadas.
* @param int $seleccion permite definir modos de selecci�n, algunos modos de selecci�n pueder ser restringidos a ciertos tipos de usuarios.
* @return array Retorna el listado de actividades, sus im�genes y sus localizaci�nes
*/
function actividadesconsulta($ID,$seleccion){
	
	global $CU, $UsuarioI, $PanelI, $FILTRO, $Freportedesde, $Freportehasta, $FILTROFECHAD, $FILTROFECHAH, $config, $Conec1;
	
	if(!isset($Freportedesde)){$Freportedesde = '9999-12-30';}
/*medicion de rendimiento lamp*/
	$starttimef = microtime(true);
	if(!isset($Freportehasta)||$Freportehasta=='0000-00-00'){$Freportehasta = '9999-12-30';}
	
	//consulta categorias utilizadas para la actividad seleccionada
	if($ID!=''){$andid = " AND `ACTcategorias`.`id_p_actividades_id` = '".$ID."'";}else{$andid='';}
	$query="
		SELECT
			`ACTcategorias`.`id`,
		    `ACTcategorias`.`id_p_actividades_id`,
		    `ACTcategorias`.`nombre`,
		    `ACTcategorias`.`descripcion`,
		    `ACTcategorias`.`orden`,
		    `ACTcategorias`.`zz_fusionadaa`,
		    CO_color
		FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
		
		WHERE
			1=1
			$andid
		
		ORDER BY 
			`ACTcategorias`.`orden` ASC
	";

	$ConsultaACTclases  = $Conec1->query($query);
	echo  $Conec1->error;	
	//echo $_SESSION['Unmapa']->DATABASE_NAME;
	//print_r($_SESSION);
	//echo $query;
	while($fila=$ConsultaACTclases->fetch_assoc()){
		$ActCat[$fila['id_p_actividades_id']][$fila['id']]=$fila;
		
		if($fila['zz_fusionadaa']>0){
			$dest=$fila['zz_fusionadaa'];
		}else{
			$dest=$fila['id'];
		}
		
		$CatConversor[$fila['id']]=$dest;
	}
 	$ConsultaACTclases->close();
	//print_r($ActCat);	
	//echo "<pre>";print_r($CatConversor);echo "</pre>";
	
	// consulta la clasificaci�n de roles seg�n sistema
	$query="
	SELECT 
		`SISroles`.`id`,
	    `SISroles`.`nombre`,
	    `SISroles`.`descripci�n`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISroles`;
	";
	$ConsultaSISroles = $Conec1->query($query);
	if($Conec1->error!=''){
		echo "error en la consulta:".$Conec1->error.PHP_EOL;
		echo $query;
	}
			
	//echo $query;
	while($row = $ConsultaSISroles->fetch_assoc()){
		$Roles[$row['id']]=$row;
	}	
	
	//consulta categorias utilizadas para la actividad seleccionada
	if($ID!=''){$andid = " AND `ACTaccesos`.`id_actividades` = '".$ID."'";}else{$andid='';}
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
			1=1
			$andid

	";
	
	$ConsultaACTaccesos = $Conec1->query($query);
	echo $Conec1->error;
	//echo $query;
	while($fila= $ConsultaACTaccesos->fetch_assoc()){
		
		$ActAcc[$fila['id_actividades']]['Acc'][$fila['nivel']][$fila['id']]=$fila;
		
		if($fila['nivel']=='2'){
			$ActAcc[$fila['id_actividades']]['acc']['editores'][$fila['id']]=$fila;
		}elseif($fila['nivel']=='1'){
			$ActAcc[$fila['id_actividades']]['acc']['participantes'][$fila['id']]=$fila;
		}		
	}
	//echo "<pre>";print_r($ActAcc);echo "</pre>";	
	
		
	
	// consulta las caracter�sticas de la actividad seleccionada	
	if($ID!=''){$andid = " AND `actividades`.`id` = '".$ID."'";}else{$andid='';}
	$query="
	 
		SELECT 
		
			`actividades`.`id`,
			`actividades`.`abierta`,
			
			`actividades`.`resumen`,
		    `actividades`.`consigna`,
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
		    `actividades`.`marco`,
		    `actividades`.`nivel`,
		    `actividades`.`zz_AUTOUSUARIOCREAC`,
		    `actividades`.`zz_AUTOFECHACREACION`,
		    `actividades`.`zz_PUBLICO`,
		    pub_publicacion_actividades.incluir_en_descarga_global,
		     pub_publicacion_actividades.incluir_en_indice,
		    usuarios.nombre as Unombre,
		    usuarios.apellido as Uapellido,
		    (select 
	    		count(1) 
	    		from 
		    		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`,
		    		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`atributos`
		    	WHERE 
		    	
		    		`geodatos`.id_actividades = `actividades`.id and `geodatos`.zz_borrada='0'
		    		AND 
		    		`geodatos`.id=`atributos`.id
		    ) cantidadPuntos
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
		
		LEFT JOIN 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.usuarios
			ON usuarios.id = actividades.zz_AUTOUSUARIOCREAC
		
		LEFT JOIN
			pub_publicacion_actividades
			ON pub_publicacion_actividades.id_p_actividades = actividades.id
			
		WHERE
			(`zz_AUTOUSUARIOCREAC`='$UsuarioI'
			OR
			`actividades`.`zz_PUBLICO` ='1')
			AND
			`actividades`.`zz_borrada` !='1'
			
			$andid
		
		ORDER BY 
			`actividades`.`zz_AUTOFECHACREACION` DESC
	
	";	
	
	$ConsultaACT  = $Conec1->query($query);
	echo $Conec1->error;
	
	$query="
	SELECT `usuarios`.`id`,
	    `usuarios`.`nombre`,
	    `usuarios`.`apellido`,
	    `usuarios`.`organizacion`,
	    `usuarios`.`area`,
	    `usuarios`.`nivel`,
	    `usuarios`.`nacimiento`,
	    `usuarios`.`mail`,
	    `usuarios`.`telefono`,
	    `usuarios`.`log`
	FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
	";	
	$ConsultaUsu = $Conec1->query($query);
	echo $Conec1->error;	
	
	while($fila=$ConsultaUsu->fetch_assoc()){
		$Usuarios[$fila['id']]=$fila;
		$lim=(date('Y')-18)."-".date('m')."01";		
		if($fila['nacimiento']>$lim){
			$Usuarios[$fila['id']]['nombre']='reservado';
			$Usuarios[$fila['id']]['apellido']='(menor)';	
			$Usuarios[$fila['id']]['mail']='(reservado)';
			$Usuarios[$fila['id']]['telefono']='(reservado)';
		}
	}
	
	if(!isset($ActAcc)){$ActAcc=array();}
	foreach($ActAcc as $Kact => $Vact){
		foreach($Vact['Acc'] as $kacN => $vacN){
			foreach($vacN as $kacc => $vacc){
				if(isset($Usuarios[$vacc['id_usuarios']])){
					$ActAcc[$Kact]['Acc'][$kacN][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];
				}			
			}
		}
	}	

				
	foreach($ActAcc as $Kact => $Vact){
		if(isset($Vact['acc'])){
			foreach($Vact['acc']['participantes'] as $kacc => $vacc){
				if(isset($Usuarios[$vacc['id_usuarios']])){
					$ActAcc[$Kact]['acc']['participantes'][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];
				}		
			}
			if(isset($Vact['acc']['editores'])){
				foreach($Vact['acc']['editores'] as $kacc => $vacc){
					
					$ActAcc[$Kact]['acc']['editores'][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];			
				}
			}
		}
	}	
		
	echo $Conec1->error;	
	
	while($fila=$ConsultaACT->fetch_assoc()){
		
		$cat['ACTcategorias']=array();
		if(!isset($ActCat[$fila['id']])){$ActCat[$fila['id']]=array();}
		$cat['ACTcategorias']=$ActCat[$fila['id']];
		
		$ACT[$fila['id']]=$fila;
		
		$a[0]=array_slice($fila,0,24);//corta el array de datos e intercala el listado de categor�as-
		$a[1]=array_slice($fila,24);
		
		$ACT[$fila['id']]=array_merge($a[0],$cat,$a[1]);	
		
		if(isset($ActAcc[$fila['id']])&&isset($ACT[$fila['id']]['acc'])){
			$ACT[$fila['id']]['acc']=$ActAcc[$fila['id']]['acc'];
		}
		$ACT[$fila['id']]['acc']['editores']['n']['id']='n';
		$ACT[$fila['id']]['acc']['editores']['n']['id_actividades']=$ID;
		$ACT[$fila['id']]['acc']['editores']['n']['id_usuarios']=$fila['zz_AUTOUSUARIOCREAC'];
		$ACT[$fila['id']]['acc']['editores']['n']['usuario']=$Usuarios[$fila['zz_AUTOUSUARIOCREAC']];	
		
		if(isset($ActAcc[$fila['id']])){
		$ACT[$fila['id']]['Acc']=$ActAcc[$fila['id']]['Acc'];
		}
		$ACT[$fila['id']]['Acc']['3']['n']['id']='n';
		$ACT[$fila['id']]['Acc']['3']['n']['id_actividades']=$ID;
		$ACT[$fila['id']]['Acc']['3']['n']['id_usuarios']=$fila['zz_AUTOUSUARIOCREAC'];
		$ACT[$fila['id']]['Acc']['3']['n']['usuario']=$Usuarios[$fila['zz_AUTOUSUARIOCREAC']];
		
		//echo "<pre>";print_r($ACT[$fila['id']]);echo "</pre>";
	}
	//echo "<pre>";print_r($ACT);echo "</pre>";
	/*if($ID!=''&&mysql_num_rows($ConsultaARG)==0){
		$ACT[]['resumen']="error en la selecci�n de la argumentaci�n";
	}*/	
	
	$query="
		SELECT `atributos`.`id`,
		    `atributos`.`valor`,
		    `atributos`.`categoria`,
		    `atributos`.`texto`,
		    `atributos`.`textobreve`, 
		    `atributos`.`link`,
		    `atributos`.`id_usuarios`,
		    `atributos`.`id_actividades`,
		    `atributos`.`fecha` as fechaA,
		    `atributos`.`escala`,
		    `atributos`.`nivelUsuario`,
		    `atributos`.`areaUsuario`
		FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`atributos`
		LEFT JOIN geodatos
		ON geodatos.id=atributos.id
		WHERE
		geodatos.zz_borrada='0'
		;
	";
	$ConsultaATT =$Conec1->query($query);
	echo $Conec1->error;	
	
	while($fila=$ConsultaATT->fetch_assoc()){
		if(!isset($CatConversor[$fila['categoria']])){$CatConversor[$fila['categoria']]='';}
		$cat=$CatConversor[$fila['categoria']];
		$ATT[$fila['id']]=$fila;
		$ATT[$fila['id']]['categoria']=$cat;
	}
	
	
	if (isset($seleccion['zoom']))
		$zoom = $seleccion['zoom'];
	else
		$zoom = 0;
	
	$query="SELECT 
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
	    `geodatos`.`fecha`
		
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
	where 
		zz_borrada='0'
		AND
		id_usuarios>0
	";
	
	
	$ConsultaGEO = $Conec1->query($query);
	echo $Conec1->error;
	if ($ConsultaGEO != null) {
		while($fila=$ConsultaGEO->fetch_assoc()){
			if(isset($ACT[$fila['id_actividades']])){
				if(!isset($ATT[$fila['id']])){continue;}
				$f=array_merge($fila,$ATT[$fila['id']]);
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]=$f;
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['Usuario']=$Usuarios[$fila['id_usuarios']];			
				if(isset($ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']])){			
					$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaTx']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['nombre'];
					$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaDes']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['descripcion'];
					$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaCo']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'];
				}
				if(!isset($ACT[$fila['id_actividades']]['categoriaspuntos'][$ATT[$fila['id']]['categoria']])){
					$ACT[$fila['id_actividades']]['categoriaspuntos'][$ATT[$fila['id']]['categoria']]=0;
				}
				$ACT[$fila['id_actividades']]['categoriaspuntos'][$ATT[$fila['id']]['categoria']]++;
			}		
		}	
	}


	return $ACT;
}	


/**
* genera listado html de actividades
*
 * 
* @param int $ID id de la argumentaci�n. null devuelve la totaclidad de argumentaci�nes cargadas por este usuario.
* @return string Retorna el listado en formato html
*/
function actividadeslistado($ID,$seleccion){
	global $CU, $UsuarioI, $PanelI, $FILTRO, $Freportedesde, $Freportehasta, $FILTROFECHAD, $FILTROFECHAH, $config,$HOY;
	
	/* consulta array de argumentaic�nes */
	$actividades = 	actividadesconsulta($ID,$seleccion);

	/* la cadeana $fila contendr� c�digo HTLM */
	$fila="
	<div class='fila titulo'>
		<div class='titulo dato descripcion'>
		Descripci�n
		</div><div class='titulo dato fecha'>
		Fecha
		</div><div class='titulo dato autor'>
		Autor
		</div><div class='titulo dato carga'>
		Dato
		</div><div class='titulo dato consigna'>
		Consigna
		</div><div class='titulo dato resultado'>
		Resultados
		</div>
	</div>
	";
	$filas[]=$fila;	
	
	foreach($actividades as $actividad){
		if($actividad['zz_PUBLICO']!='1'&&$actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){continue;}
		if($actividad['resumen']!=''){$resumen=$actividad['resumen'];}else{$resumen="-vacio-";}	
			
		if($actividad['zz_PUBLICO']!='1'){$prelim='preliminar';$ptx='actividad a publicar<br>';}else{$prelim='';$ptx='';}				
		$fila="
			<div class='fila $prelim'>
				<div class='dato descripcion'>
					<a href='./actividad.php?actividad=".$actividad['id']."'>".$ptx.$resumen."</a>
				</div><div class='dato fecha'>
					".$actividad['desde']." ".$actividad['hasta']."
				</div><div class='dato autor'>
					".$actividad['Unombre']." ".$actividad['Uapellido']."
				</div><div class='dato carga'>
				".$actividad['cantidadPuntos']."
				</div>";

		$tx=substr($actividad['consigna'],0,300);
			
		$fila.= "<div title='".$actividad['consigna']."' class='dato consigna'>".substr($actividad['consigna'],0,300)."</div>";			
	
		if($actividad['hasta']<=$HOY&&$actividad['hasta']>'0000-00-00'){
			$estado='cerrada';
			$tx="Ha cerrado el ".$actividad['hasta'].".";

			if($actividad['resultados']==''){$tx.="Sin resultados cargados por el equipo coordinador.";
			
			}else{
				$tit=$actividad['resultados'];
				$tx.="<br>".substr($actividad['resultados'],0,100)."(...)";
			}
					
			
		}elseif($HOY<$actividad['desde']){
			$tx="Abrir� el d�a ".$actividad['desde'].", para la carga de datos.";
			$estado='pendiente';
		}else{
			$tx='Esta actividad permanece activa.';
			$estado='activa';
			
			if($actividad['hasta']>'0000-00-00'){
			$tx.='Cierre est� progrmado para '.$actividad['hasta'].".";	
			}
		}
		
			if(!isset($tit)){$tit='';}
		$fila.= "<div estado='".$estado."' title='".$tit."' class='dato resultado'>$tx</div>";
		
		
		//$fila.=$datoloc;
		$fila.=	"</div>";
					

		$filas[]=$fila;
	}
	$resultado="";
	foreach($filas as $f){
		$resultado.=$f;
	}
	if(count($actividades)==0){
		$resultado='No se han registrado actividades.';
	}
	
	return $resultado;
}

/**
* genera listado array usuario registrados
*
* @param int $ID restringe la b�squeda a una actividad espec�fica **PENDIENTE**
* @return string Retorna el listado en formato array
*/
function usuariosconsulta($ID){
	global $CU, $UsuarioI, $Conec1;
	
		$query="
	SELECT `usuarios`.`id`,
	    `usuarios`.`nombre`,
	    `usuarios`.`apellido`,
	    `usuarios`.`organizacion`,
	    `usuarios`.`area`,
	    `usuarios`.`nivel`,
	    `usuarios`.`nacimiento`,
	    `usuarios`.`mail`,
	    `usuarios`.`telefono`,
	    `usuarios`.`log`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
	";	
	$ConsultaUsu = $Conec1->query($query);
	echo $Conec1->error;
	
	while($fila=$ConsultaUsu->fetch_assoc()){
		$Usuarios[$fila['id']]=$fila;		
	}
	
	return($Usuarios);
}

?>
