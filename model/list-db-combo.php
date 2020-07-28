<?php 

/*
* Carrega o combo de empresas: para INSERT carrega todos, para UPDATE coloca a empresa selecionada em primeiro
*/


include_once "class/Sql.php";
$conn=new Sql();

$idcia=0;
$iddb=0;

if (isset($_SESSION['iddb'])) :
    $iddb = $_SESSION['iddb'];
endif;

if (isset($_SESSION['idcia'])) :
    $idcia = $_SESSION['idcia'];
else:
    $idcia = $_SESSION['s_idcia'];
endif;


if ($_SESSION['s_superuser']) $idcia = '%';

//echo '<select class="col-md-4 input-large form-control" id="iddb" name="iddb" style="margin-bottom: 15px; margin-left:15px"  autofocus required>';


if ($iddb == 0): /* inclusão*/

    echo "<option value=''</option>";    
    
    
    $result   = $conn->sql(basename(__FILE__), "SELECT DISTINCT db.iddb, db.dbname
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat
                                                 INNER JOIN adm_cias cia
                                                    ON cia.idcia = cat.idcia
                                                 WHERE cat.idcia like :IDCIA",
                                                 array(":IDCIA" => $idcia));


else:
   
    $result   = $conn->sql(basename(__FILE__), "SELECT DISTINCT db.iddb, db.dbname
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat   
                                                 INNER JOIN adm_cias cia                                                 
                                                    ON cia.idcia = cat.idcia
                                                 WHERE db.iddb = $iddb                                                   
                                                 UNION 
                                                 SELECT DISTINCT db.iddb, db.dbname
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat
                                                 INNER JOIN adm_cias cia
                                                    ON cia.idcia = cat.idcia
                                                 WHERE cat.idcia like :IDCIA",
					                             array(":IDCIA" => $idcia));
                      
endif;


foreach ($result as $key => $value) {

    echo "
    <option value=".$result[$key]['iddb'].">".$result[$key]['dbname']."</option>";        

};

//echo "</select>";

?>

