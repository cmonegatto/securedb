<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once "class/Sql.php";

$idcat     = $data['idcat'];
$dayAccess  = $data['dayAccess'];
$dayRules   = $data['dayRules'];

$conn1=new Sql();
$conn2=new Sql();

$result1 = $conn1->sql( basename(__FILE__), "SELECT hostname, username, password, aliasdb, dbname, port, player, iddb
											  FROM adm_databases
											 WHERE idcat = :IDCAT
											 ORDER BY dbname",
											 array(":IDCAT" => $idcat));
                                             
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
        google.charts.setOnLoadCallback(drawChartAcessos);

        function drawChartAcessos() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Sem regra', 'Bloqueado'],
            ['db-mysql', 50, 20],
            ['db-oracle', 22, 2],
            ['db-sqlserver', 7, 33],
            ['db-mysql2', 45, 10]
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Incidencia de acessos nos últimos <?php echo $dayAccess*-1?> dias',
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
        google.charts.setOnLoadCallback(drawChartCredenciaisCobertas);

        function drawChartCredenciaisCobertas() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', '%Cobertura'],
            ['db-mysql', 50],
            ['db-oracle', 22],
            ['db-sqlserver', 87],
            ['db-mysql2', 45]
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
                title: 'Regras não utilizadas últimos <?php echo $dayRules*-1?> dias',
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

