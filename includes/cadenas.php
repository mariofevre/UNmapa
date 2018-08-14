<?php 
/**
* cadenas.php
*
* cadena se incorpora en la carpeta includes 
* ya que contiene funciones genéricas de operación de cadenas (strings)
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 TReCC SA
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



header('Content-Type:text/html; charset=cp-1252');

/**
* genera una cadena aleatoria compatible con nombre de archivo (solo letras del alfabeto)
*
* @param integer $Largo cantidad de caracteres esperados
* @return string Retorna una cadena aleatoria compatible con nombre de archivo (solo letras del alfabeto)(diciembre de 2013)
*/
function cadenaArchivo( $Largo ) {
	$habilitados = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

	$largobase = strlen( $habilitados );
	for( $i = 0; $i < $Largo; $i++ ) {
		$resultado .= $habilitados[ rand( 0, $largobase - 1 ) ];
	}

	return $resultado;
}

/**
* genera un explode a partr de múltiples delimitadores
*
* @param array $delim array de delimietadores
* @param string $dato texto a explotar
* @return string Retorna un array resultado de los sucesivos explode(diciembre de 2013)
*/
function explodemulti($delim,$dato) {
	$array = explode($delim[0],$dato);
    array_shift($delim);
    foreach($array as $key => $texto) {
         $array[$key] = explodemulti($delim, $texto);
    }
    return  $array;
}

/**
* analiza que una cadena solo tenga caracteres válidos para una dirección de mail
*
* @param string $mail dirección de email
* @return boolean Retorna true o false
*/
function mailvalido($mail) {
	$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.@";
	for($i=0 ; $i < strlen($mail) ; $i++){
		if(strpos($permitidos, $mail[$i]) === false)
		return false;
	}
	
	$necesarios="@.";
	for($i=0 ; $i < strlen($necesarios) ; $i++){
		if(strpos($mail, $necesarios[$i]) === false)
		return false;
	}	
	
	return true;		
}


/**
* analiza que una cadena solo tenga caracteres válidos para un nombre de usuario (log)
*
* @param string $log cuenta
* @return boolean Retorna true o false
*/
function logvalido($log){
	$permitidos = "abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ0123456789_-";
	
	if(strlen($log)>4){
		for($i=0 ; $i < strlen($log) ; $i++)
		{
			if(strpos($permitidos, $log[$i]) === false)
			return false;
		}
		return true;	
	}
	
	return false;
}
/**
* analiza que una cadena solo tenga caracteres válidos para una contraseña
*
* @param string $pass cuenta
* @return boolean Retorna true o false
*/
function passvalido($pass){
	$permitidos = " abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ0123456789_-";
	
	if(strlen($pass)>4){
		for($i=0 ; $i < strlen($pass) ; $i++)
		{
			if(strpos($permitidos, $pass[$i]) === false)
			return false;
		}
		return true;			
	}
	return false;
}

/**
* codifica algunos caracteres ascii
*
* @param string $nombre nombre real 
* @return boolean Retorna true o false
*/
function decodeascii(){
	
}



/**
* analiza que una cadena solo tenga caracteres letras de la a a la z y espacios
*
* @param string $nombre nombre real 
* @return boolean Retorna true o false
*/
function nombrevalido($nombre) {
	$permitidos = " abcdefghijklmnñopqrstuvwxyzáéíóúàèìòùäëïöüABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ";
	
	for($i=0 ; $i < strlen($nombre) ; $i++){
		$a = strpos($permitidos, $nombre[$i]);
		if($a === false){
			return false;
	
		}
	}
	return true;
}

/**
* analiza que una cadena se válida como teléfono
*
* @param string $tel
* @return boolean Retorna true o false
*/
function telvalido($tel) {
	$permitidos = " 0123456789-/()";
	
	if(strlen($tel)>4){
	for($i=0 ; $i < strlen($tel) ; $i++)
		{
			if(strpos($permitidos, $tel[$i]) === false)
			return false;
		}
	return true;	
	}
	return false;
}

/**
* analiza una formula y determina si es segura para ejecutar desde php. Solo permite matemática básica
*
* @param string $formula fórmula a evaluar
* @return boolean Retorna true o false
*/
function formulaphpsegura($formula) {
	$permitidos = "123456789()[]+-*/ ";
	
	for($i=0 ; $i < strlen($tel) ; $i++)
		{
			if(strpos($permitidos, $tel[$i]) === false){
				return false;
			}
		}
	return true;	

}


/**
* cambia los acentos web por acentos tx
*
* @param string 
* @return string
*/
function eliminarCodigoAsciiAcentos($texto) {
	
	$tit = str_replace(array("\r\n", "\r", "\n"), " ", $texto);
	$tit = str_replace("&aacute;", "á", $tit);
	$tit = str_replace("&Aacute;", "Á", $tit);
	$tit = str_replace("&eacute;", "é", $tit);
	$tit = str_replace("&Eacute;", "É", $tit);
	$tit = str_replace("&iacute;", "í", $tit);
	$tit = str_replace("&Iacute;", "Í", $tit);
	$tit = str_replace("&oacute;", "ó", $tit);
	$tit = str_replace("&Oacute;", "Ó", $tit);
	$tit = str_replace("&uacute;", "ú", $tit);
	$tit = str_replace("&Uacute;", "Ú", $tit);
	$tit = str_replace("&ntilde;", "ñ", $tit);
	$tit = str_replace("&Ntilde;", "Ñ", $tit);
	$tit = str_replace("&nbsp;", " ", $tit);
	$tit = str_replace("&ndash;", "-", $tit);
	$tit = str_replace("&ldquo;", "`", $tit);
	$tit = str_replace("&rdquo;", "`", $tit);	
	$tit = str_replace("&nbsp;", " ", $tit);
	
	return $tit;	

}




function eliminarTildes($cadena){
 
    //Codificamos la cadena en formato utf8 en caso de que nos de errores
    //$cadena = utf8_encode($cadena);
 
    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );
 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );
 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );
 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );
 /*
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );
 */
    return $cadena;
}
