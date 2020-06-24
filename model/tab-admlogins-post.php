<?php 

/*
* Carrega o combo de empresas: para INSERT carrega todos, para UPDATE coloca a empresa selecionada em primeiro
*/

session_start();

include_once "../class/Sql.php";
$conn=new Sql();

$idcat = $_REQUEST['idcat'];
$iddb = $_SESSION['iddb'];

/*
if ($iddb  == "" ):

    $result   = $conn->sql(basename(__FILE__), 
                "SELECT DISTINCT db.iddb, db.dbname
                FROM adm_categories cat
                LEFT JOIN adm_databases db
                ON cat.idcat=db.idcat
                INNER JOIN adm_cias cia
                ON cia.idcia = cat.idcia
                WHERE cat.idcat = $idcat"
    );    

else:
*/
    $result   = $conn->sql(basename(__FILE__), 
                "SELECT DISTINCT db.iddb, db.dbname
                FROM adm_categories cat
                LEFT JOIN adm_databases db
                ON cat.idcat=db.idcat
                INNER JOIN adm_cias cia
                ON cia.idcia = cat.idcia
                WHERE cat.idcat = $idcat
                  AND db.iddb = $iddb
                UNION 
                SELECT DISTINCT db.iddb, db.dbname
                FROM adm_categories cat
                LEFT JOIN adm_databases db
                ON cat.idcat=db.idcat
                INNER JOIN adm_cias cia
                ON cia.idcia = cat.idcia
                WHERE cat.idcat = $idcat
                  AND db.iddb <> $iddb"
    );

//endif;


foreach ($result as $key => $value) {

    $iddb_post[] = array(
        'iddb'	 => $result[$key]['iddb'],
        'dbname' => $result[$key]['dbname']);
 
};

// caso não encontre linhas no select limpa o combo...
if ( !isset($iddb_post) ) :
    $iddb_post[] = array(
        'iddb'	 => "",
        'dbname' => "");
endif;

echo(json_encode($iddb_post));

?>

