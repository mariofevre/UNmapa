<?php 
/**
* mensajedesarrollo.php
*
* mensajedesarrollo.php se incorpora en la carpeta raiz en tanto resulta una de las funcionesb�sicas para el funcionamiento de la aplicacion 
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	general
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2014 TReCC SA
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


if($UsuarioI!=''){
echo"<div class='recuadros ventanadesarrollo'>";
foreach($_SESSION['DEBUG']['mensajes'] as $mensaje){
	echo "<p>";
	echo $mensaje;
	echo "</p>";
}
echo"</div>";

unset($_SESSION['DEBUG']['mensajes']);
}