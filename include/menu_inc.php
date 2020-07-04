<?php

/* Verificar ROLE do usuario para dar permissão aos menus */

if (!isset($_SESSION['s_iduser'])):
	$user = 0;
	$admin = 0;
	$superuser = 0;

elseif ($_SESSION['s_superuser']):
	$user = 1;
	$admin = 1; // se for SUPER USER já acima do AMIN
	$superuser = $_SESSION['s_superuser'];
else:	
	$user = 1;
	$admin = $_SESSION['s_admin'];
	$superuser = $_SESSION['s_superuser'];
endif;

?>


<nav class="navbar navbar-expand-lg navbar-light bg-dark">
	<h2><a class="navbar-brand btn btn-warning" href="\">2Now Consulting</a></h2>
		

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
		

	<div class="collapse navbar-collapse" id="navbarSupportedContent">

		<ul class="navbar-nav mr-auto">

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-white-50" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Cadastros básicos</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item <?= ($superuser)? '': 'disabled'?>" href="/company">Companhias</a>
						<a class="dropdown-item <?= ($admin)? '': 'disabled'?>" 	href="/users">Usuários</a>
						<a class="dropdown-item <?= ($admin)? '': 'disabled'?>" 	href="/categories">Categoria de ambientes</a>
						<a class="dropdown-item <?= ($admin)? '': 'disabled'?>" href="/databases">Bancos de dados</a>
					</div>
				</li>

				<li class="nav-item active">
					<a class="nav-link text-white-50 <?= ($user)? '': 'disabled'?>" href="/admloginslog/0/0">Gestão de Acessos<span class="sr-only">(current)</span></a>
				</li>


<!--
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-white-50" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gestão de acessos</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item <?= ($user)? '': 'disabled'?>" href="\admtools">Aplicações bloqueadas</a>
						<a class="dropdown-item <?= ($user)? '': 'disabled'?>" href="#">Lock Down de usuário</a>				
						<div class="dropdown-divider"></div> 
						<a class="dropdown-item <?= ($user)? '': 'disabled'?>" href="#">Gestão das regras de acesso</a>
						<a class="dropdown-item <?= ($user)? '': 'disabled'?>" href="\admloginslog/0/0">Acessos suspeiros</a>
					</div>
				</li>
-->

					
				<li class="nav-item active">
					<a class="nav-link text-white-50" href="/logout">Sair<span class="sr-only">(current)</span></a>
				</li>

				<!--
				<li class="nav-item">
					<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
				</li>
				-->

		</ul>


		<form class="form-inline my-2 my-lg-0">
			<?php
				if (isset($_SESSION['s_nameuser'])):
					echo "<span style='color:white'>Olá {$_SESSION['s_nameuser']} </span>";
				endif;
			?>

		</form>

<!--
		<form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" type="search" aria-label="Search">
			<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
		</form>
-->

	</div>
</nav>

