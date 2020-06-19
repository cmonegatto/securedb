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

echo '<select class="col-md-4 input-large form-control" id="idcat" name="idcat" style="margin-bottom: 15px; margin-left:15px"  autofocus required>';


if ($iddb == 0): /* inclusão*/

    echo "<option value=''</option>";    
    
    
    $result   = $conn->sql(basename(__FILE__), "SELECT DISTINCT cat.idcat, cat.category, cat.idcia, cia.cianame
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat
                                                 INNER JOIN adm_cias cia
                                                    ON cia.idcia = cat.idcia
                                                 WHERE cat.idcia like '$idcia'"
                        );    


else:
   
    $result   = $conn->sql(basename(__FILE__), "SELECT DISTINCT cat.idcat, cat.category, cat.idcia, cia.cianame
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat   
                                                 INNER JOIN adm_cias cia                                                 
                                                    ON cia.idcia = cat.idcia
                                                 WHERE db.iddb = $iddb                                                   
                                                 UNION 
                                                 SELECT DISTINCT cat.idcat, cat.category, cat.idcia, cia.cianame
                                                  FROM adm_categories cat
                                                  LEFT JOIN adm_databases db
                                                    ON cat.idcat=db.idcat
                                                 INNER JOIN adm_cias cia
                                                    ON cia.idcia = cat.idcia
                                                 WHERE cat.idcia like '$idcia'"
                                                   //AND db.iddb <> $iddb"
                        );    

                      
endif;


foreach ($result as $key => $value) {
    
    if ($_SESSION['s_superuser']):
        $category = $result[$key]['category'] . " - " . $result[$key]['cianame'];
    else:
        $category = $result[$key]['category'];
    endif;


    echo "    
    <option value=".$result[$key]['idcat'].">".$category."</option>";    

};

echo "</select>";

?>

