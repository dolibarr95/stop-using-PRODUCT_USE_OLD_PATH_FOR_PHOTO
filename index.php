<?php
/**
 *	Move the picture form old to new path for Dolibarr.
 *
 *	\warning	/!\ ALWAYS BACKUP YOUR FILE BEFORE RUNNING THIS CODE /!\
 *
 *	\todo		Before running this code save this file on you server as a php file.
 *
 *	\todo		One you have run this code don't foreget to delete or set to 0 the constant PRODUCT_USE_OLD_PATH_FOR_PHOTO. And delete this file from you server.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 */	
 
 
// parameters to configure START 
 
 /**
 *	\var	string		$conf	Path to the conf.php file
 */  
$conf	= '../conf/conf.php';

 /**
 *	\var	string		$dir	Path to the products pictures
 */ 
$dir	= '/home/server/data/produit/';

// parameters to configure END 



include($conf);
$db = new mysqli($dolibarr_main_db_host, $dolibarr_main_db_user, $dolibarr_main_db_pass, $dolibarr_main_db_name);
if ($db->connect_errno) {
    printf("Connection error : %s\n", $db->connect_error.'<br>');
    die();
}


for($i=0;$i<10;$i++){//first level of directory (eg /home/server/data/produit/1)
	
	for($e=0;$e<10;$e++){//second level of directory (eg /home/server/data/produit/1/8)
		
		$idliste = scandir($dir.$i.'/'.$e.'/');//list of all in this directory
		
		foreach($idliste as $id){//list files and directories inside $dir.$i.'/'.$e.'/'
			if(is_numeric($id)){ //third level of directory (eg /home/server/data/produit/1/8/15781)
				if(is_dir($dir.$i.'/'.$e.'/'.$id.'/photos/')){//check directory photos (eg /home/server/data/produit/1/8/15781/photos)
					
					$contenu = scandir($dir.$i.'/'.$e.'/'.$id.'/photos/');//list of all in this directory
											
					$sql  = "SELECT";
					$sql .= " p.ref";
					$sql .= " FROM ".$dolibarr_main_db_prefix."product AS p";
					$sql .= " WHERE p.rowid = ".$id;
					$resql = $db->query($sql);
	
					if ($resql ){
						
						if( $resql->num_rows == 1  ){//this product exists in database
							
							$obj = $resql->fetch_object();
					
							foreach($contenu as $fichiers){//for each element of this directory
								
								if(is_file($dir.$i.'/'.$e.'/'.$id.'/photos/'.$fichiers)){//only files not directory
						
									if(!is_dir($dir.$obj->ref.'/')){//if directory doesn't exist create it
										mkdir($dir.$obj->ref.'/', 0755);
									}
									
									if(is_dir($dir.$obj->ref.'/')){
										
										print 'the dir : '.$dir.$obj->ref.'/'.' exists'.'<br>';	
										
										if(rename($dir.$i.'/'.$e.'/'.$id.'/photos/'.$fichiers, $dir.$obj->ref.'/'.$fichiers )){//faster than copy and paste
											print 'transfert this file :'.$dir.$i.'/'.$e.'/'.$id.'/photos/'.$fichiers.' in '.$dir.$obj->ref.'/'.$fichiers.'<br>';	
										}else{
											print '<b style="color:red">unable to transfert this file :'.$dir.$i.'/'.$e.'/'.$id.'/photos/'.$fichiers.' in '.$dir.$obj->ref.'/'.$fichiers.'</b><br>';	
										}

									}else{
										print '<b style="color:red">the dir : '.$dir.$obj->ref.'/'.' doesn\'t exist'.'</b><br>';	
									}
									
								print '<hr>';
								   
							 
								}elseif($fichiers == 'thumbs'){//list of all in thumbs
							 
									$icones = scandir($dir.$i.'/'.$e.'/'.$id.'/photos/thumbs/');
									foreach($icones as $icone){
									
										if(is_file($dir.$i.'/'.$e.'/'.$id.'/photos/thumbs/'.$icone)){
							 
												if(!is_dir($dir.$obj->ref.'/thumbs/')){//if directory doesn't exist create it
													mkdir($dir.$obj->ref.'/thumbs/', 0755);
												}
												
												if( is_dir($dir.$obj->ref.'/thumbs/') ){
													
													print 'the dir : '.$dir.$obj->ref.'/thumbs/'.' exists'.'<br>';	
													
													if(rename($dir.$i.'/'.$e.'/'.$id.'/photos/thumbs/'.$icone, $dir.$obj->ref.'/thumbs/'.$icone )){//faster than copy and paste
														print 'transfert this file :'.$dir.$i.'/'.$e.'/'.$id.'/photos/thumbs/'.$icone.' in '.$dir.$obj->ref.'/thumbs/'.$icone.'<br>';	
													}else{
														print '<b style="color:red">unable to transfert this file :'.$dir.$i.'/'.$e.'/'.$id.'/photos/thumbs/'.$icone.' in '.$dir.$obj->ref.'/thumbs/'.$icone.'</b><br>';	
													}
													
												}else{
													print '<b style="color:red">the dir : '.$dir.$obj->ref.'/thumbs/'.' doesn\'t exist'.'</b><br>';	
												}
												
										print '<hr>';		
				
										}	
									}
								}
							}		
						}
					}					
				}
			}
		}
	}
}
?>
