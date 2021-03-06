<div class="navbar-inner">
	<div class="container-fluid">
		<!-- BEGIN LOGO -->
		<a class="brand">
		<img src="assets/img/big-logo-new.png" alt="logo" />
		</a>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
		<img src="assets/img/menu-toggler.png" alt="" />
		</a>          
		<!-- END RESPONSIVE MENU TOGGLER -->				
		<!-- BEGIN TOP NAVIGATION MENU -->		
		<?php
		//In this section we will count the number of tasks assigned to the current session user
		$bugManager = new BugManager($pdo);
		$taskManager = new TaskManager($pdo);
        $taskNumber = $taskManager->getTaskNumberByUser($_SESSION['userMerlaTrav']->login());
        $bugNumber = $bugManager->getBugsNumber();
		?>			
		<ul class="nav pull-right">
			<li class="dropdown" id="header_inbox_bar">
				<a href="tasks.php" class="dropdown-toggle">
				<i class="icon-tasks"></i>  
				<?php
				if ( $taskNumber > 0 ) {
				?>
				<span class="badge"><?= $taskNumber ?></span>
				<?php  
                }
                ?>
				</a>
			</li>
			<li class="dropdown" id="header_inbox_bar">
                <a href="bugs.php" class="dropdown-toggle">
                <i class="icon-warning-sign"></i>  
                <?php
                if ( $bugNumber > 0 ) {
                ?>
                <span class="badge"><?= $bugNumber ?></span>
                <?php  
                }
                ?>
                </a>
            </li>
			<!-- BEGIN USER LOGIN DROPDOWN -->
			<li class="dropdown user">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<img alt="" src="assets/img/avatar_small.png" />
				<span class="username"><?= $_SESSION['userMerlaTrav']->login(); ?></span>
				<i class="icon-angle-down"></i>
				</a>
				<ul class="dropdown-menu">
					<li><a href="user-profil.php"><i class="icon-user"></i> Mon Compte</a></li>
					<li class="divider"></li>
					<li><a href="logout.php"><i class="icon-key"></i> Se déconnecter</a></li>
				</ul>
			</li>
			<!-- END USER LOGIN DROPDOWN -->
		</ul>
		<!-- END TOP NAVIGATION MENU -->	
	</div>
</div>