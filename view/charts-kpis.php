<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once "class/Sql.php";
include "function/utils.php";

/*

-- MYSQL
select ifnull(last_updated_by, created_by) username, count(*) 
  from adm_logins 
 group by ifnull(last_updated_by, created_by) ;
 

-- ORACLE
select decode(last_updated_by, null, created_by) username, count(*)   
  from adm_logins  
 group by decode(last_updated_by, null, created_by) ;
 

-- SQLSERVER
 select isnull(last_updated_by, created_by), count(*)
  from adm_logins
 group by isnull(last_updated_by, created_by);

*/


$idcat      = $data['idcat'];
$dayAccess  = $data['dayAccess']*-1;
$dayRules   = $data['dayRules']*-1;

$conn1=new Sql();
$conn2=new Sql();

$ArrayCredenciaisCobertas   = []; //[['Databases', '%Cobertura']];
$ArrayAcessos               = [];
$ArrayRegras                = [];
$ArraySuperUsers            = [];
$ArrayOperadores            = [];


$result1 = $conn1->sql( basename(__FILE__), 
    "SELECT hostname, username, password, aliasdb, dbname, port, player, iddb
       FROM adm_databases
      WHERE idcat = :IDCAT
      ORDER BY dbname",
      array(":IDCAT" => $idcat));


foreach ($result1 as $key1 => $value) {

    $localhost	= $result1[$key1]['hostname'];
    $username	= $result1[$key1]['username'];
    $password	= encrypt_decrypt('decrypt', $result1[$key1]['password']);
    $aliasdb	= $result1[$key1]['aliasdb'];
    $dbname		= $result1[$key1]['dbname'];
    $port		= $result1[$key1]['port'];
    $player		= $result1[$key1]['player'];
    $iddb		= $result1[$key1]['iddb'];


    $_SESSION['msg'] = '';

    $conn2=new Sql($player, $localhost, $username, $password, $dbname, $port);


    $aliasdb	= strtolower($result1[$key1]['aliasdb']);
    $dbname		= strtolower($result1[$key1]['dbname']);

    if (strlen($_SESSION['msg']) == 0 ):

        if ($player == 'OCI'):

            $CredenciaisCobertas= $conn2->sql( basename(__FILE__), 
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                    ( SELECT count(*) as total_users
                                        FROM dba_users
                                       WHERE account_status='OPEN') users,
                                    ( SELECT count(username) kill_users			
                                        FROM securedb.adm_logins_to_kill) k"
                                );

            $Acessos = $conn2->sql( basename(__FILE__), 
                                "SELECT * FROM 
                                    (SELECT count(*) as SUSPECT
                                       FROM adm_logins_log
                                      WHERE datetime>= trunc(sysdate)-$dayAccess
                                        AND killed is null) s,
                                    (SELECT count(*) as KILLED
                                       FROM adm_logins_log
                                      WHERE datetime>= trunc(sysdate)-$dayAccess
                                        AND killed is not null) k"
                                );

            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE last_used_date <= TRUNC(sysdate)-$dayRules OR last_used_date IS NULL"                                
                                );

            $SuperUsers = $conn2->sql( basename(__FILE__), 
                                "SELECT COUNT(*) as TOTAL_USERS 
                                   FROM dba_role_privs rp, dba_users u
                                  WHERE granted_role='DBA'
                                    AND rp.grantee = u.username
                                    AND u.account_status='OPEN'"                                
                                );


        elseif ($player == 'SQLSRV'):

            $CredenciaisCobertas= $conn2->sql( basename(__FILE__),
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                (SELECT count(*) as total_users
                                   FROM sys.server_principals 
		                          WHERE is_disabled=0
                                    AND type <>'R'
                                    AND name NOT LIKE 'NT%SERVI%'
                                    AND type_desc not in ('WINDOWS_GROUP')
                                    AND name NOT LIKE '##%') as users,
                                (SELECT count(username) kill_users			
                                   FROM adm_logins_to_kill) as k"
                                );

            $Acessos = $conn2->sql( basename(__FILE__), 
                                "SELECT * FROM 
                                    (SELECT count(*) as SUSPECT
                                       FROM adm_logins_log
                                      WHERE datetime >= cast(GETDATE()-$dayAccess as date)
                                        AND killed is null) s,
                                    (SELECT count(*) as KILLED
                                       FROM adm_logins_log
                                      WHERE datetime >= cast(GETDATE()-$dayAccess as date)
                                        AND killed is not null) k"
                                );


            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE last_used_date <= cast(GETDATE()-$dayRules as date) OR last_used_date IS NULL"                                
                                );


            $SuperUsers = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS
                                   FROM sys.server_principals a
                                   JOIN master..syslogins b ON a.sid = b.sid
                                  WHERE a.type <> 'R'
                                    AND b.sysadmin = 1
                                    AND a.name NOT LIKE '##%'
                                    AND a.name NOT LIKE 'NT%SERV%'
                                    AND is_disabled=0"                                
                                );

        elseif ($player == 'MYSQL'):

            $CredenciaisCobertas= $conn2->sql( basename(__FILE__), 
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                    (SELECT count(distinct user) total_users 		FROM mysql.user) 		 as users,
                                    (SELECT count(username)      kill_users			FROM adm_logins_to_kill) as k"
                                );


            $Acessos = $conn2->sql( basename(__FILE__), 
                                "SELECT * FROM 
                                    (SELECT count(*) as SUSPECT
                                       FROM adm_logins_log
                                      WHERE datetime >= date_sub(now(), interval $dayAccess day)
                                        AND killed is null) s,
                                    (SELECT count(*) as KILLED
                                       FROM adm_logins_log                                      
                                      WHERE datetime >= date_sub(now(), interval $dayAccess day)
                                        AND killed is not null) k"
                                );

                                
            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE last_used_date <= date_sub(now(), interval $dayRules day) OR last_used_date IS NULL"
                                );


            $SuperUsers = $conn2->sql( basename(__FILE__), 
                                "SELECT count(distinct user) as TOTAL_USERS 
                                   FROM mysql.user 
                                  WHERE super_priv='Y'"                                
                                );


        endif;


		foreach ($CredenciaisCobertas as $key2 => $value) {
            array_push($ArrayCredenciaisCobertas, [$aliasdb, round( ($CredenciaisCobertas[$key2]['KILL_USERS']/$CredenciaisCobertas[$key2]['TOTAL_USERS'])*100, 1) ]);
		};

		foreach ($Acessos as $key2 => $value) {
            array_push($ArrayAcessos, [$aliasdb, $Acessos[$key2]['SUSPECT'], $Acessos[$key2]['KILLED'] ]);
		};

		foreach ($Regras as $key2 => $value) {
            array_push($ArrayRegras, [$aliasdb, $Regras[$key2]['TOTAL_USERS'] ]);
		};

		foreach ($SuperUsers as $key2 => $value) {
            array_push($ArraySuperUsers, [$aliasdb, $SuperUsers[$key2]['TOTAL_USERS'] ]);
		};


    endif;                                            
};

/*
foreach ($ArrayCredenciaisCobertas as $key => $value){
    echo '[' . $ArrayCredenciaisCobertas[$key][0] . ', ' . $ArrayCredenciaisCobertas[$key][1] . '], ';
};
exit();
*/

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

        google.charts.load('current', {'packages':['bar']});

        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartCredenciaisCobertas);

        function drawChartCredenciaisCobertas() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', '%Cobertura'],
        <?php
            foreach ($ArrayCredenciaisCobertas as $key => $value){
                echo '["' . $ArrayCredenciaisCobertas[$key][0] . '", ' . $ArrayCredenciaisCobertas[$key][1] . '], ';
            };
        ?>

            /*
            ['Databases', '%Cobertura'],
            ['db-mysql', 50],
            ['db-oracle', 22],
            ['db-sqlserver', 87],
            ['db-mysql2', 45],
            */
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Cobertura de Credenciais',
                subtitle: 'Alvo: 100%'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            bars: 'horizontal' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('Chart-CredenciaisCobertas'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        }



        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartAcessos);

        function drawChartAcessos() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Sem regra', 'Bloqueado'],
            <?php
                foreach ($ArrayAcessos as $key => $value){
                    echo '["' . $ArrayAcessos[$key][0] . '", ' . $ArrayAcessos[$key][1] . ', ' . $ArrayAcessos[$key][2] . '], ';
                };
            ?>

            /*
            ['db-mysql', 50, 20],
            ['db-oracle', 22, 2],
            ['db-sqlserver', 7, 33],
            ['db-mysql2', 45, 10]
            */
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Incidencia de acessos nos últimos <?php echo $dayAccess ?> dias',
                subtitle: 'Alvo 0%'
            },
            legend: { position: 'top', textStyle: { fontSize: 10 } },
            //colors: ['#FFF68F','pink'],
            colors: ['#FFF68F','#FF6A6A'],
            backgroundColor: 'whitesmoke',
            bars: 'horizontal'
        };

        var chart = new google.charts.Bar(document.getElementById('chart-acessos'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
        google.visualization.events.addListener(chart,'select',selectHandler);

        /* Esse site tem alguns eventos para gerar link nas barras e tratar dados...
        https://forums.asp.net/t/2120745.aspx?Chart+report+with+drill+down+featureusing+Google+Chart+Api
        */

        }

        function selectHandler()
        {
            alert('working progress...');

        }

        

        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartRegras);
        function drawChartRegras() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            <?php
                foreach ($ArrayRegras as $key => $value){
                    echo '["' . $ArrayRegras[$key][0] . '", ' . $ArrayRegras[$key][1] . '], ';
                };
            ?>
            /*
            ['db-mysql', 2],
            ['db-oracle', 5],
            ['db-sqlserver', 1],
            ['db-mysql2', 11]
            */
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Regras não utilizadas últimos <?php echo $dayRules ?> dias',
                subtitle: 'Alvo: 0'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            bars: 'horizontal', // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('Chart-Regras'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        }


        
        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartSuperUsers);
        function drawChartSuperUsers() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            <?php
                foreach ($ArraySuperUsers as $key => $value){
                    echo '["' . $ArraySuperUsers[$key][0] . '", ' . $ArraySuperUsers[$key][1] . '], ';
                };
            ?>
            /*
            ['db-mysql', 2],
            ['db-oracle', 135],
            ['db-sqlserver', 4],
            ['db-mysql2', 11]
            */
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Privilégios de Super User',
                subtitle: 'Alvo: baixo'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            bars: 'horizontal', // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('Chart-SuperUsers'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        }


       
        /* ---------------------------------------------------------------------- */
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawChartOperadores);
        function drawChartOperadores() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            ['Cláudio Monegatto', 70],
            ['José Antonio', 15],
            ['Maria do Rosario', 5],
            ['Lidia Macedo', 10]
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
                title: 'Operadores da Solução',
                //pieHole: 0.4,
                is3D: true,
            legend: { position: 'labeled', textStyle: { fontSize: 10 } },
            titleTextStyle: { fontSize: 15, color: "gray", bold: false },
            backgroundColor: 'whitesmoke',
        };

        //var chart = new google.charts.Bar(document.getElementById('Chart-Operadores'));
        //chart.draw(data, google.charts.Bar.convertOptions(options));

        var chart = new google.visualization.PieChart(document.getElementById('Chart-Operadores'));
        chart.draw(data, options);

        }


</script>

    <div class="container">

        <div class="row">

            <div class="col-sm-6"   id="Chart-CredenciaisCobertas"  style="width: 400px;  height: 230px; padding-top:0px;"></div>
            <div class="col-sm-6"   id="chart-acessos"              style="width: 400px;  height: 230px; padding-top:0px;"></div> 
            <div class="col-sm-6"   id="Chart-Regras"               style="width: 400px;  height: 230px; padding-top:15px;"></div>
            <div class="col-sm-6"   id="Chart-SuperUsers"           style="width: 400px;  height: 230px; padding-top:15px;"></div>
            <div class="col-sm-6"   id="Chart-Operadores"            style="width: auto;  height: auto; padding-top:20px;"></div>

        </div>
    </div>


<?php include_once 'include/footer_inc.php' ?>

