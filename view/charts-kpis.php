<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once "class/Sql.php";
include "function/utils.php";


$idcat     = $data['idcat'];
$dayAccess  = $data['dayAccess']*-1;
$dayRules   = $data['dayRules']*-1;

$conn1=new Sql();
$conn2=new Sql();

$ArrayCredenciaisCobertas = []; //[['Databases', '%Cobertura']];
$ArrayChartAcessos = [];

$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, aliasdb, dbname, port, player, iddb
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

            $result2= $conn2->sql( basename(__FILE__), 
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                    ( SELECT count(*) as total_users
                                        FROM dba_users
                                       WHERE account_status='OPEN') users,
                                    ( SELECT count(username) kill_users			
                                        FROM securedb.adm_logins_to_kill) k"
                                );

            $lista = $conn2->sql( basename(__FILE__), 
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


        elseif ($player == 'SQLSRV'):

            $result2= $conn2->sql( basename(__FILE__),
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                (SELECT count(*) as total_users
                                   FROM sys.server_principals 
                                  WHERE type_desc='SQL_LOGIN' and  is_disabled=0) as users,
                                (SELECT count(username) kill_users			
                                   FROM adm_logins_to_kill) as k"
                                );

            $lista = $conn2->sql( basename(__FILE__), 
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



        elseif ($player == 'MYSQL'):

            $result2= $conn2->sql( basename(__FILE__), 
                                "SELECT users.total_users as TOTAL_USERS, k.kill_users as KILL_USERS
                                   FROM
                                    (SELECT count(distinct user) total_users 		FROM mysql.user) 		 as users,
                                    (SELECT count(username)      kill_users			FROM adm_logins_to_kill) as k"
                                );


            $lista = $conn2->sql( basename(__FILE__), 
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
        endif;


		foreach ($result2 as $key2 => $value) {

			$total_users    = $result2[$key2]['TOTAL_USERS'];
			$kill_users     = $result2[$key2]['KILL_USERS'];

            // alias, e percentual de cobertura por banco de dados...
            array_push($ArrayCredenciaisCobertas, [$aliasdb, round( ($kill_users/$total_users)*100, 1) ]);

		};

		foreach ($lista as $key2 => $value) {

			$suspect    = $lista[$key2]['SUSPECT'];
			$killed     = $lista[$key2]['KILLED'];

            // alias, e percentual de cobertura por banco de dados...
            array_push($ArrayChartAcessos, [$aliasdb, $suspect, $killed ]);

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

    /*

    ------------- MYSQL
    select users.total_users, k.kill_users
    from
        (select count(*) 		total_users 		from mysql.user) 		 as users,
        (select count(username) kill_users			from adm_logins_to_kill) as k;


    ------------- SQLSERVER
    select users.total_users, k.kill_users
      from
		( select count(*) as total_users
			from sys.server_principals 
		   where type_desc='SQL_LOGIN' and  is_disabled=0) as users,
		( select count(username) kill_users			
			from adm_logins_to_kill) as k;

    
    ------------- ORACLE
    select users.total_users, k.kill_users
    from
        ( select count(*) as total_users
            from dba_users
        where account_status='OPEN') users,
        ( select count(username) kill_users			
            from securedb.adm_logins_to_kill) k;

    */


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
            foreach ($ArrayChartAcessos as $key => $value){
                echo '["' . $ArrayChartAcessos[$key][0] . '", ' . $ArrayChartAcessos[$key][1] . ', ' . $ArrayChartAcessos[$key][2] . '], ';
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
            alert('foi foi foi...');

        }

        

        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartRegras);
        function drawChartRegras() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            ['db-mysql', 2],
            ['db-oracle', 5],
            ['db-sqlserver', 1],
            ['db-mysql2', 11]
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
            ['db-mysql', 2],
            ['db-oracle', 135],
            ['db-sqlserver', 4],
            ['db-mysql2', 11]
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


</script>

    <div class="container">

        <div class="row">

            <div class="col-sm-6"   id="Chart-CredenciaisCobertas"  style="width: 400px;  height: 230px; padding-top:0px;"></div>
            <div class="col-sm-6"   id="chart-acessos"              style="width: 400px;  height: 230px; padding-top:0px;"></div> 
            <div class="col-sm-6"   id="Chart-Regras"               style="width: 400px;  height: 230px; padding-top:15px;"></div>
            <div class="col-sm-6"   id="Chart-SuperUsers"           style="width: 400px;  height: 230px; padding-top:15px;"></div>

        </div>
    </div>


<?php include_once 'include/footer_inc.php' ?>

