<?php

include_once 'include/header_inc.php';
include_once 'include/menu_inc.php';
include_once "class/Sql.php";
include "function/utils.php";


/* 
funão para ordenação array multidimensional 
uso: $arrayTst = array_orderby($arrayTst, 'quantidade', SORT_DESC, 'database', SORT_ASC);
*/
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}



$idcat      = $data['idcat'];
$dayAccess  = $data['dayAccess']*-1;
$dayRules   = $data['dayRules']*-1;

$conn1=new Sql();
$conn2=new Sql();

$ArrayCredenciaisCobertas   = []; 
$ArrayRegras                = [];
$ArrayAcessos               = [];
$ArraySuperUsers            = [];
$ArrayOperadores            = [];
$ArrayOcorrencias           = [];
$ArrayAcessosTOP            = [];
$ArrayAcessosGeral          = [];
$ArrayTentativas            = [];
$ArrayStatusTrigger         = [];
$ArrayDBVersion             = [];

$result1 = $conn1->sql( basename(__FILE__), 
    "SELECT hostname, username, password, aliasdb, dbname, port, player, iddb
       FROM adm_databases
      WHERE idcat = :IDCAT
      ORDER BY dbname",
      array(":IDCAT" => $idcat));


$msgALL = 'Ocorreu erro de abertura nas seguintes instâncias: '; //acumula mensagens de erros de abertura dos bancos depois envia para MSG da SESSION Global
$msgErro = 0; // se ocorrer algum erro seta 1. Ao final se >0 então alimenta MSG da Global Session 

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
                                        AND killed = '*') k,
                                    (SELECT count(*) as KILLED_AFTER
                                       FROM adm_logins_log
                                      WHERE datetime>= trunc(sysdate)-$dayAccess
                                        AND killed = '#') ka"
                                );

            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE NVL(last_used_date,created_date) <= TRUNC(sysdate)-$dayRules"                                
                                );

            $SuperUsers = $conn2->sql( basename(__FILE__), 
                                "SELECT COUNT(*) as TOTAL_USERS 
                                   FROM dba_role_privs rp, dba_users u
                                  WHERE granted_role='DBA'
                                    AND rp.grantee = u.username
                                    AND u.account_status='OPEN'"                                
                                );

            $Operadores = $conn2->sql( basename(__FILE__), 
                                "SELECT decode(last_updated_by, null, created_by, last_updated_by) as USERNAME, count(*) as TOTAL_USERS
                                   FROM adm_logins  
                               GROUP BY decode(last_updated_by, null, created_by, last_updated_by)"                                
                                );

            $AcessosTOP = $conn2->sql( basename(__FILE__), 
                                "SELECT USERNAME, count(*) as TOTAL_ACCESS    
                                   FROM adm_logins_log
                                  WHERE datetime>= trunc(sysdate)-$dayAccess
                                    -- AND killed is not null
                               GROUP BY USERNAME"
                                );

            $AcessosGeral = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as QUANTIDADE, to_char(datetime, 'mon') as MESEXTENSO, to_char(datetime, 'mm') as MES, to_char(datetime, 'yyyy') as ANO
                                   FROM adm_logins_log
                                  WHERE datetime >= TO_CHAR(ADD_MONTHS(sysdate-to_char(sysdate, 'DD')+1,-5),'DD-MON-YYYY')
                                  GROUP BY to_char(datetime, 'mon'), to_char(datetime, 'mm'), to_char(datetime, 'yyyy')
                                  ORDER BY 4,3"
                                );

            $Tentativas = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_ACCESS    
                                   FROM mv_dba_audit_session 
                                  WHERE timestamp >= trunc(sysdate)-$dayAccess
                                    AND returncode=1017         -- falha na tentativa de login: usuário inexistente ou senha incorreta
                                    AND action_name='LOGON'     -- tentar ajudar o otimizador oracle
                                "
                                );

            $StatusTrigger = $conn2->sql( basename(__FILE__), 
                                "SELECT decode(status, 'ENABLED', 1, 0) STATUS
                                   FROM dba_triggers 
                                  WHERE trigger_name ='SECDB_T'"
                                );

            $DBVersion = $conn2->sql( basename(__FILE__), 
                                'SELECT BANNER as VERSION 
                                   FROM "V$VERSION" 
                                  WHERE ROWNUM=1'
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
                                        AND killed = '*') k,
                                    (SELECT count(*) as KILLED_AFTER
                                       FROM adm_logins_log
                                      WHERE datetime >= cast(GETDATE()-$dayAccess as date)
                                        AND killed = '#') ka"
                                );


            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE CASE 
                                            WHEN last_used_date IS NULL 
                                            THEN created_date 
                                            ELSE last_updated_date 
                                        END <= cast(GETDATE()-$dayRules as date)"
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

                                
            $Operadores = $conn2->sql( basename(__FILE__), 
                                "SELECT isnull(last_updated_by, created_by) as USERNAME, count(*) as TOTAL_USERS
                                   FROM adm_logins
                               GROUP BY isnull(last_updated_by, created_by);"                                
                                );

            $AcessosTOP = $conn2->sql( basename(__FILE__), 
                                "SELECT USERNAME, count(*) as TOTAL_ACCESS    
                                   FROM adm_logins_log
                                  WHERE datetime >= cast(GETDATE()-$dayAccess as date)
                                    -- AND killed is not null
                                    GROUP BY USERNAME"
                                );

            $AcessosGeral = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as QUANTIDADE, lower(left( DATENAME ( month, datetime ),3)) as MESEXTENSO, month(datetime) as MES, year(datetime) as ANO
                                   FROM adm_logins_log
                                  WHERE datetime >= cast(dateadd(month, -5, getdate()-DAY(getdate()-1)) as date)
                                  GROUP BY LEFT( DATENAME ( month, datetime ),3), month(datetime), year(datetime)
                                  ORDER BY 4,3"
                                );


            $Tentativas = $conn2->sql( basename(__FILE__), 
                                "EXEC sp_acessoslog $dayAccess", 
                                );

            $StatusTrigger = $conn2->sql( basename(__FILE__), 
                                "SELECT case when (is_disabled = 0) then 1 else 0 end as STATUS
                                   FROM sys.server_triggers 
                                  WHERE name like '%T_LOGON'"
                                );

            $DBVersion = $conn2->sql( basename(__FILE__), 
                                "SELECT
                                    CASE
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '8%' THEN 'SQL2000'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '9%' THEN 'SQL2005'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '10.0%' THEN 'SQL2008'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '10.5%' THEN 'SQL2008 R2'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '11%' THEN 'SQL2012'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '12%' THEN 'SQL2014'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '13%' THEN 'SQL2016'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '14%' THEN 'SQL2017'
                                        WHEN CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) like '15%' THEN 'SQL2019'
                                        ELSE 'unknown'
                                    END
                                    + ' - ' + CONVERT(VARCHAR(128), SERVERPROPERTY('ProductLevel'))
                                    + ' - ' + CONVERT(VARCHAR(128), SERVERPROPERTY('Edition'))
                                    + ' - ' + CONVERT(VARCHAR(128), SERVERPROPERTY('ProductVersion')) as VERSION
                                    "
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
                                      WHERE datetime >= date(date_sub(now(), interval $dayAccess day))
                                        AND killed is null) s,
                                    (SELECT count(*) as KILLED
                                       FROM adm_logins_log                                      
                                      WHERE datetime >= date(date_sub(now(), interval $dayAccess day))
                                        AND killed = '*') k,
                                    (SELECT count(*) as KILLED_AFTER
                                       FROM adm_logins_log                                      
                                      WHERE datetime >= date(date_sub(now(), interval $dayAccess day))
                                        AND killed = '#') ka"
                                );

                                
            $Regras = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as TOTAL_USERS 
                                   FROM adm_logins
                                  WHERE ifnull(last_used_date,created_date) <= date(date_sub(now(), interval $dayRules day))"
                                );


            $SuperUsers = $conn2->sql( basename(__FILE__), 
                                "SELECT count(distinct user) as TOTAL_USERS 
                                   FROM mysql.user 
                                  WHERE super_priv='Y'"                                
                                );


            $Operadores = $conn2->sql( basename(__FILE__), 
                                "SELECT ifnull(last_updated_by, created_by) as USERNAME, count(*) as TOTAL_USERS
                                   FROM adm_logins 
                               GROUP BY ifnull(last_updated_by, created_by)"                                
                                );


            $AcessosTOP = $conn2->sql( basename(__FILE__), 
                                "SELECT USERNAME, count(*) as TOTAL_ACCESS    
                                   FROM adm_logins_log
                                  WHERE datetime >= date(date_sub(now(), interval $dayAccess day))
                                    -- AND killed is not null
                                    GROUP BY USERNAME"                                    
                                );


            $AcessosGeral = $conn2->sql( basename(__FILE__), 
                                "SELECT count(*) as QUANTIDADE, month(datetime) as MES, year(datetime) as ANO,
                                 CASE 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'feb' THEN 'fev' 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'apr' THEN 'abr' 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'aug' THEN 'ago' 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'sep' THEN 'set' 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'oct' THEN 'out' 
                                    WHEN lower( left( DATE_FORMAT(datetime, '%M'), 3)) = 'dec' THEN 'dez' 
                                    ELSE lower( left( DATE_FORMAT(datetime, '%M'), 3))
                                 END as MESEXTENSO
                                   FROM adm_logins_log
                                  WHERE datetime >= date(date_sub(date_sub(now(), interval 5 month), interval day(now())-1 day))
                                  GROUP BY month(datetime), year(datetime)
                                  ORDER BY 3,4"
                                );

            $Tentativas = $conn2->sql( basename(__FILE__), 
                                "SELECT 0 as TOTAL_ACCESS"
                                );


            $StatusTrigger = $conn2->sql( basename(__FILE__), 
                                "SELECT if(GLOBAL_VALUE like '%p_logon%', 1, 0) as STATUS
                                   FROM INFORMATION_SCHEMA.SYSTEM_VARIABLES 
                                  WHERE VARIABLE_NAME = 'INIT_CONNECT'"
                                );                                

            $DBVersion = $conn2->sql( basename(__FILE__), 
                                "SELECT VERSION() as VERSION"
                            );
        endif;



		foreach ($CredenciaisCobertas as $key2 => $value) 
        {
            $ArrayCredenciaisCobertas[] = array('database'=> $aliasdb, 'kill_users'=> round( ($CredenciaisCobertas[$key2]['KILL_USERS']/$CredenciaisCobertas[$key2]['TOTAL_USERS'])*100, 1) );
		};  $ArrayCredenciaisCobertas = array_orderby($ArrayCredenciaisCobertas, 'kill_users', SORT_ASC, 'database', SORT_ASC);



		foreach ($Regras as $key2 => $value) 
        {
            $ArrayRegras[] = array( 'database'=> $aliasdb, 'total_users' => $Regras[$key2]['TOTAL_USERS'] );
		};  $ArrayRegras = array_orderby($ArrayRegras, 'total_users', SORT_DESC, 'database', SORT_ASC);


        
		foreach ($Acessos as $key2 => $value) {
            $ArrayAcessos[] = array( 'database'=> $aliasdb, 'suspect'=> $Acessos[$key2]['SUSPECT'], 
                                     'killed'=> $Acessos[$key2]['KILLED'], 
                                     'killed_after'=> $Acessos[$key2]['KILLED_AFTER'], 
                                     'total'=> $Acessos[$key2]['SUSPECT'] + $Acessos[$key2]['KILLED'] + $Acessos[$key2]['KILLED_AFTER']  );
		};  $ArrayAcessos = array_orderby($ArrayAcessos, 'total', SORT_DESC);


		foreach ($SuperUsers as $key2 => $value) {
            $ArraySuperUsers[] = array( 'database'=> $aliasdb, 'total_users'=> $SuperUsers[$key2]['TOTAL_USERS'] );
		};  $ArraySuperUsers = array_orderby($ArraySuperUsers, 'total_users', SORT_DESC, 'database', SORT_ASC);



        foreach ($Operadores as $key2 => $value) {
            if ( isset($ArrayOperadores) ):
                $index = array_search($Operadores[$key2]['USERNAME'], array_column($ArrayOperadores, 'username')); // procura se já tem esse USERNAME no array          
            endif;

            if (isset($index) && gettype($index)== 'integer'): // se o tipo da variavel for integer significa que encontrou um valor numerico da posição do dado no array, senão a variavel fica boolean 
                $ArrayOperadores[$index]['total_users']=$ArrayOperadores[$index]['total_users']+$Operadores[$key2]['TOTAL_USERS'];
            else:
                $ArrayOperadores[] = array('username'=> $Operadores[$key2]['USERNAME'], 'total_users'=> $Operadores[$key2]['TOTAL_USERS'] );
            endif;
        };  $ArrayOperadores = array_orderby($ArrayOperadores, 'total_users', SORT_DESC);



		foreach ($AcessosTOP as $key2 => $value) {
            $ArrayAcessosTOP[] = array( 'username'=> $AcessosTOP[$key2]['USERNAME'] . " ($aliasdb)", 'total_access'=> $AcessosTOP[$key2]['TOTAL_ACCESS'] );
		};  $ArrayAcessosTOP = array_orderby($ArrayAcessosTOP, 'total_access', SORT_DESC);




        foreach ($AcessosGeral as $key2 => $value) {
            if ( isset($ArrayAcessosGeral) ):
                $index = array_search($AcessosGeral[$key2]['MES'], array_column($ArrayAcessosGeral, 'mes')); // procura se já tem esse USERNAME no array          
            endif;

            if (isset($index) && gettype($index)== 'integer'): // se o tipo da variavel for integer significa que encontrou um valor numerico da posição do dado no array, senão a variavel fica boolean 
                $ArrayAcessosGeral[$index]['quantidade']=$ArrayAcessosGeral[$index]['quantidade']+$AcessosGeral[$key2]['QUANTIDADE'];
            else:
                $ArrayAcessosGeral[] = array('mesano'=> $AcessosGeral[$key2]['MESEXTENSO'] . '-' . substr($AcessosGeral[$key2]['ANO'], -2), 
                                         'quantidade'=> $AcessosGeral[$key2]['QUANTIDADE'],
                                                'mes'=> $AcessosGeral[$key2]['MES'],
                                                'ano'=> $AcessosGeral[$key2]['ANO'] );
            endif;
        };  $ArrayAcessosGeral = array_orderby($ArrayAcessosGeral, 'ano', SORT_ASC, 'mes', SORT_ASC);


        
        foreach ($Tentativas as $key2 => $value) 
        {
            $ArrayTentativas[] = array( 'database'=> $aliasdb, 'total_access' => $Tentativas[$key2]['TOTAL_ACCESS'] );
		};  $ArrayTentativas = array_orderby($ArrayTentativas, 'total_access', SORT_DESC, 'database', SORT_ASC);


        foreach ($StatusTrigger as $key2 => $value) 
        {
            $ArrayStatusTrigger[] = array( 'database'=> $aliasdb, 'STATUS' => $StatusTrigger[$key2]['STATUS'] );
		};  $ArrayStatusTrigger = array_orderby($ArrayStatusTrigger, 'STATUS', SORT_ASC, 'database', SORT_ASC);


        foreach ($DBVersion as $key2 => $value) {
            if ( isset($ArrayDBVersion) ):
                $index = array_search($DBVersion[$key2]['VERSION'], array_column($ArrayDBVersion, 'version')); // procura se já tem esse USERNAME no array          
            endif;

            if (isset($index) && gettype($index)== 'integer'): // se o tipo da variavel for integer significa que encontrou um valor numerico da posição do dado no array, senão a variavel fica boolean 
                $ArrayDBVersion[$index]['qtd']=$ArrayDBVersion[$index]['qtd'] + 1;
            else:
                $ArrayDBVersion[] = array('version'=> $DBVersion[$key2]['VERSION'], 'qtd'=> 1 );
            endif;
        };  $ArrayDBVersion = array_orderby($ArrayDBVersion, 'qtd', SORT_DESC, 'version', SORT_ASC);

    else:
        $msgALL = $msgALL . "($aliasdb)" . " "; // incremento da mensagem com os databases que tiveram erro de abertura
        $msgErro =1;                            // seta se ocorreu algum erro de abertura. É usado após o loop para checar...
    endif;                                            

};

// Se ocorram erros acima na abertura de algum banco, seta a mensagem global
if ($msgErro > 0):
    $_SESSION['msg'] = $msgALL . " - Verifique (usuário/senha/host/porta)"."<br/><br/>";
endif;
    

/* calcula o tamanho do height para os gráficos, dependendo de quantos bancos existem (tamanho vertical variável) */
$height = count($ArrayCredenciaisCobertas)*20;
$height = $height <= 200 ? 200 : $height;

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

        google.charts.load('current', {'packages':['bar']});

        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartCredenciaisCobertas);

        function drawChartCredenciaisCobertas() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Cobertura'],
        <?php
            foreach ($ArrayCredenciaisCobertas as $key => $value){
                echo '["' . $ArrayCredenciaisCobertas[$key]['database'] . '", ' . $ArrayCredenciaisCobertas[$key]['kill_users']/100 . '], ';
            };
        ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Cobertura de Credenciais por database',
                subtitle: 'Alvo: 100%'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            hAxis: { format: '#%', viewWindow:{ min:0, max:1 } },
            bars: 'horizontal' // Required for Material Bar Charts.
        };
        
        var chart = new google.charts.Bar(document.getElementById('Chart-CredenciaisCobertas'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
        
    }
    


        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartAcessos);

        function drawChartAcessos() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Sem regra', 'Bloqueado', 'Derrubado'],
            <?php
                foreach ($ArrayAcessos as $key => $value){
                    //echo '["' . $ArrayAcessos[$key]['database'] . '", ' . $ArrayAcessos[$key]['suspect'] . ', ' . $ArrayAcessos[$key]['killed'] . ', ' . '], ';
                    echo '["' . $ArrayAcessos[$key]['database'] . '", ' . $ArrayAcessos[$key]['suspect'] . ', ' . $ArrayAcessos[$key]['killed'] . ', ' . $ArrayAcessos[$key]['killed_after'] . ', ' . '], ';
                };
            ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                /*  title: 'Incidencia de acessos - último(s) <?php echo $dayAccess ?> dia(s)', */
                title: 'Incidencia de acessos <?php echo $dayAccess==0 ? "(HOJE)" : "-$dayAccess\ dia(s)" ?> ',
                subtitle: 'Alvo 0'
            },
            legend: { position: 'top', textStyle: { fontSize: 10 } },
            backgroundColor: 'whitesmoke',
            bars: 'horizontal',
            //groupWidth: '75%',
            isStacked: true,
            series: {0:{color: '#FFF68F', visibleInLegend: true}, 1:{color: '#FF4040', visibleInLegend: true}, 2:{color: 'darkgray', visibleInLegend: true} },
            hAxis: { viewWindow:{ min:0 } }
            //series: {0:{color: '#FFF68F', visibleInLegend: true}, 1:{color: '#FF6A6A', visibleInLegend: true}}
            //series: {0:{color: 'gold', visibleInLegend: true}, 1:{color: 'red', visibleInLegend: true}}
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
                    echo '["' . $ArrayRegras[$key]['database'] . '", ' . $ArrayRegras[$key]['total_users'] . '], ';
                };
            ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                //title: 'Regras não utilizadas último(s) <?php echo $dayRules ?> dia(s)',
                title: 'Regras não utilizadas <?php echo "-$dayRules dia(s)" ?> ',
                subtitle: 'Alvo: 0'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            //colors: ['#104E8B'],
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            hAxis: { viewWindow:{ min:0 } },
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
                    echo '["' . $ArraySuperUsers[$key]['database'] . '", ' . $ArraySuperUsers[$key]['total_users'] . '], ';                    
                };
            ?>
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
            //colors: ['#104E8B'],
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            hAxis: { viewWindow:{ min:0 } },
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
            ['Nome', '%Atividades'],
            <?php
                foreach ($ArrayOperadores as $key => $value){
                    echo '["' . $ArrayOperadores[$key]['username'] . '", ' . $ArrayOperadores[$key]['total_users'] . '], ';
                };
            ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
                title: 'Operadores da Solução',
                //pieHole: 0.4,
                is3D: true,
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            chartArea:{left:0}, //,top:20, width:"70%",height:"70%"},
            titleTextStyle: { fontSize: 15, color: "gray", bold: false },
            backgroundColor: 'whitesmoke',
        };

        //var chart = new google.charts.Bar(document.getElementById('Chart-Operadores'));
        //chart.draw(data, google.charts.Bar.convertOptions(options));

        var chart = new google.visualization.PieChart(document.getElementById('Chart-Operadores'));
        chart.draw(data, options);

        }


        /* ---------------------------------------------------------------------- */
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawChartAcessosTOP);
        function drawChartAcessosTOP() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            <?php
                foreach ($ArrayAcessosTOP as $key => $value){
                    if ($key >= 10): break; endif; // Mostrar somente 10 registros do array (TOP 10)
                    echo '["' . $ArrayAcessosTOP[$key]['username'] . '", ' . $ArrayAcessosTOP[$key]['total_access'] . '], ';
                };
            ?>                    
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
                //title: 'TOP 10 ocorrencias de acessos último(s) <?php echo $dayAccess ?> dia(s)',
                title: 'TOP 10 ocorrencias de acessos <?php echo $dayAccess==0 ? "(HOJE)" : "(-$dayAccess\d)" ?> ',      
                //pieHole: 0.4,
                is3D: true,
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            chartArea:{left:0}, //,top:20, width:"70%",height:"70%"},
            titleTextStyle: { fontSize: 15, color: "gray", bold: false },
            backgroundColor: 'whitesmoke',
        };

        //var chart = new google.charts.Bar(document.getElementById('Chart-Operadores'));
        //chart.draw(data, google.charts.Bar.convertOptions(options));

        var chart = new google.visualization.PieChart(document.getElementById('Chart-AcessosTOP'));
        chart.draw(data, options);

        }




      google.charts.setOnLoadCallback(drawChartAcessosGeral);

      function drawChartAcessosGeral() {
        var data = google.visualization.arrayToDataTable([
            ['Meses', 'Quantidade'],
            <?php
                foreach ($ArrayAcessosGeral as $key => $value){
                    if ($key >= 10): break; endif; // Mostrar somente 10 registros do array (TOP 10)
                    echo '["' . $ArrayAcessosGeral[$key]['mesano'] . '", ' . $ArrayAcessosGeral[$key]['quantidade'] . '], ';
                };
            ?>       
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
          chart: {
            title: 'Histórico geral de ocorrências de acesso (6 meses)' ,
            subtitle: 'Alvo: próximo de zero',
          },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            colors: ['lightblue'],
            is3D: true,
            backgroundColor: 'whitesmoke'
        };

        var chart = new google.charts.Bar(document.getElementById('chart-AcessoGeral'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
       

      }      

  

        
        /* ---------------------------------------------------------------------- */
        google.charts.setOnLoadCallback(drawChartTentativa);
        function drawChartTentativa() {
        var data = google.visualization.arrayToDataTable([
            ['Databases', 'Quantidade'],
            <?php
                foreach ($ArrayTentativas as $key => $value){
                    echo '["' . $ArrayTentativas[$key]['database'] . '", ' . $ArrayTentativas[$key]['total_access'] . '], ';                    
                };
            ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                //title: 'Tentavias de quebra de senha último(s) <?php echo $dayAccess ?> dia(s)',
                title: 'Tentavias de quebra de senha <?php echo $dayAccess==0 ? "(HOJE)" : "(-$dayAccess\d)" ?> ',
                subtitle: 'Alvo: 0'
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            //colors: ['#104E8B'],
            colors: ['RED'],
            backgroundColor: 'whitesmoke',
            hAxis: { viewWindow:{ min:0 } },
            bars: 'horizontal', // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('Chart-Tentativa'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        }



        /* ---------------------------------------------------------------------- */

        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawChartDBVersion);
        function drawChartDBVersion() {
        var data = google.visualization.arrayToDataTable([
            ['Versão Database', 'Quantidade'],
            <?php
                foreach ($ArrayDBVersion as $key => $value){
                    echo '["' . $ArrayDBVersion[$key]['version'] . '", ' . $ArrayDBVersion[$key]['qtd'] . '], ';
                };
            ?>                    
        ]);
        
        var options = {
            chartArea: {
                backgroundColor: {
                fillOpacity: 0.1 }},
                title: 'Sumarização das versões dos Databases',      
                //pieHole: 0.4,
                is3D: true,
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            chartArea:{left:0}, //,top:20, width:"70%",height:"70%"},
            titleTextStyle: { fontSize: 15, color: "gray", bold: false },
            backgroundColor: 'whitesmoke',
        };

        var chart = new google.visualization.PieChart(document.getElementById('Chart-DBVersion'));
        chart.draw(data, options);

        }


        /*
        google.charts.setOnLoadCallback(drawChartDBVersion);
        function drawChartDBVersion() {
        var data = google.visualization.arrayToDataTable([
            ['Versão Database', 'Quantidade'],
            <?php
                foreach ($ArrayDBVersion as $key => $value){
                    echo '["' . $ArrayDBVersion[$key]['version'] . '", ' . $ArrayDBVersion[$key]['qtd'] . '], ';                    
                };
            ?>
        ]);

        var options = {
            chartArea: {
                backgroundColor: {
                //fill: '#FF0000',
                fillOpacity: 0.1 }},
            chart: {
                title: 'Sumarização das versões dos Databases',      
                subtitle: ''
            },
            legend: { position: 'center', textStyle: { fontSize: 10 } },
            //colors: ['#104E8B'],
            colors: ['lightblue'],
            backgroundColor: 'whitesmoke',
            //hAxis: { viewWindow:{ min:0 } },
            bars: 'horizontal', // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('Chart-DBVersion'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        }
        */

</script>

    <div class="container">

        <div class="row">
            <!--
            <div class="col-sm-6"   id="Chart-CredenciaisCobertas"  style="width: 400px;  height: 230px; padding-top:0px;"></div>
            <div class="col-sm-6"   id="chart-acessos"              style="width: 400px;  height: 230px; padding-top:0px;"></div> 
            <div class="col-sm-6"   id="Chart-Regras"               style="width: 400px;  height: 230px; padding-top:15px;"></div>
            <div class="col-sm-6"   id="Chart-SuperUsers"           style="width: 400px;  height: 230px; padding-top:15px;"></div>
            <div class="col-sm-6"   id="Chart-Operadores"            style="width: auto;  height: auto; padding-top:20px;"></div>

            <div class="col-sm-6"   id="Chart-CredenciaisCobertas"  style="width: auto;  height: <?php echo $height?>px;    padding-top:10px;"></div>
            <div class="col-sm-6"   id="Chart-Regras"               style="width: auto;  height: <?php echo $height?>px;    padding-top:10px;"></div>
            <div class="col-sm-6"   id="chart-acessos"              style="width: auto;  height: <?php echo $height?>px ;   padding-top:20px;"></div> 
            <div class="col-sm-6"   id="Chart-AcessosTOP"           style="width: 300px; height: 300px;                     padding-top:20px;"></div>
            <div class="col-sm-6"   id="Chart-SuperUsers"           style="width: auto;  height: <?php echo $height?>px;    padding-top:10px;"></div>
            <div class="col-sm-6"   id="Chart-Operadores"           style="width: 300px; height: 300px;                     padding-top:10px;"></div>
            -->
            <div class="col-sm-6"   id="chart-AcessoGeral"                          style="width: 300px; height: <?php echo $height?>px;    padding-top:10px;"></div>            
            <div class="col-sm-6 verticalLine"   id="Chart-CredenciaisCobertas"     style="width: 300px;  height: <?php echo $height?>px;    padding-top:10px;"></div>

            <div class="col-sm-6"   id="chart-acessos"                              style="width: 300px;  height: <?php echo $height?>px ;   padding-top:20px;"></div> 
            <div class="col-sm-6 verticalLine"   id="Chart-AcessosTOP"              style="width: 300px; height: 300px;                     padding-top:20px;"></div>

            <div class="col-sm-6"   id="Chart-Tentativa"                            style="width: 300px;  height: <?php echo $height?>px;    padding-top:20px;"></div>
            <div class="col-sm-6 verticalLine"   id="Chart-SuperUsers"              style="width: 300px;  height: <?php echo $height?>px;    padding-top:20px;"></div>

            <div class="col-sm-6"   id="Chart-Regras"                               style="width: 300px;  height: <?php echo $height?>px;    padding-top:20px;"></div>
            <div class="col-sm-6 verticalLine"   id="Chart-Operadores"              style="width: 300px; height: 300px;                     padding-top:10px;"></div>

          
            
            <div class="col-sm-4"                                                   style="width: 300px; height: 300px;                     padding-top:10px;">
                Status Controle de Acessos (agora)
                <table class="table table-sm table-dark table-hover table-bordered">
                    <thead>
                        <tr>
                        <th style='width:20%; text-align:center' scope="col">Status</th>
                        <th style='text-align:left' scope="col">Database</th>
                        <th style='width:20%; text-align:center' scope="col">Versão</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($ArrayStatusTrigger as $key => $value) {
                                echo "<tr>";
                                $str = $ArrayStatusTrigger[$key]['STATUS'] ? 'lightgreen' : 'lightcoral';
                                echo "<td style='text-align: center'><i style='color: $str'; class='fa fa-circle fa-lg'></i></td>";                                                              
                                echo "<td>".$ArrayStatusTrigger[$key]['database']."</td>";
                                echo "<td style='text-align: center'> - </td>";
                                echo "</tr>";
                            };
                        ?>

                    </tbody>
                </table>               
            </div>

            <div class="col-sm-2 "></div>            
            <div class="col-sm-6 verticalLine"   id="Chart-DBVersion"           style="width: 300px; height: 300px ;   padding-top:20px;"></div>            
            
            <div class="col-sm-12 ">
                <hr/>
            </div>
                       
       </div>

<?php include_once 'include/footer_inc.php' ?>

