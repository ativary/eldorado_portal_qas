<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <title><?= (isset($_titulo) ? strip_tags($_titulo)." | " : "").NOME_PORTAL; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url('public/assets/images/favicon.ico'); ?>">

        <!-- App css -->
        <link href="<?= base_url('public/assets/css/bootstrap.min.css').'?v'.VERSION_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('public/assets/css/icons.css').'?v'.VERSION_CSS; ?>" rel="stylesheet" type="text/css" />
        <?php if(MENU_VERTICAL): ?><link href="<?= base_url('public/assets/css/metismenu.min.css').'?v'.VERSION_CSS; ?>" rel="stylesheet" type="text/css" /><?php endif; ?>
        <link href="<?= base_url('public/assets/css/'.((MENU_VERTICAL) ? 'style' : 'style_h').'.css').'?v'.VERSION_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('public/assets/css/custom.css').'?v'.VERSION_CSS; ?>" rel="stylesheet" type="text/css" />

        <script src="<?= base_url('public/assets/js/jquery.min.js').'?v'.VERSION_JS; ?>"></script>
        <script>
            const base_url = '<?= base_url(); ?>';
            const selecionaColigada = (codColigada) => {

                let dados = {
                    "codcoligada": codColigada,
                }

                if(dados.codcoligada == ""){ exibeAlerta("error", "<b>Coligada</b> não informada."); return false; }

                openLoading();

                $.ajax({
                    url: "<?= base_url('portal/action/seleciona_coligada'); ?>",
                    type:'POST',
                    data:dados,
                    success:function(result){

                        var response = JSON.parse(result);

                        exibeAlerta(response.tipo, response.msg, 2, '<?= base_url(); ?>');

                    },
                });

            }
            const selecionaRegistro = (registro) => {

                let dados = {
                    "registro": registro,
                }

                if(dados.registro == ""){ exibeAlerta("error", "<b>Chapa</b> não informada."); return false; }

                openLoading();

                $.ajax({
                    url: "<?= base_url('portal/action/seleciona_chapa'); ?>",
                    type:'POST',
                    data:dados,
                    success:function(result){

                        var response = JSON.parse(result);

                        if(response.tipo != "warning"){
                            exibeAlerta(response.tipo, response.msg, 2, '<?= base_url(); ?>');
                        }else{
                            exibeAlerta(response.tipo, response.msg, 6);
                            openLoading(true);
                        }
                        

                    },
                });

            }
        </script>

    </head>

<body>

    <!-- Top Bar Start -->
    <div class="topbar noprint">
            <!-- Navbar -->
            <nav class="navbar-custom">

            <!-- LOGO -->
            <div class="topbar-left">
                <a href="<?= base_url(); ?>" class="logo">
                    <span>
                        <img src="<?= base_url('public/assets/images/logo-dark.png'); ?>" alt="logo-large">
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topbar-nav float-right mb-0">

                <li class="mt-4 hidden-sm">
                    <div class="alert alert-secondary pt-1 pb-1" role="alert">
                        <i class="fas fa-user-alt"></i> - <?= session()->get('log_nome'); ?>
                    </div>
                </li>

                <?php if(1 == 0): // exibe icone notificações ?>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i class="mdi mdi-bell-outline nav-icon"></i>
                        <span class="badge badge-danger badge-pill noti-icon-badge">2</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                        <!-- item-->
                        <h6 class="dropdown-item-text">
                            Notifications (258)
                        </h6>
                        <div class="slimscroll notification-list">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item active">
                                <div class="notify-icon bg-success"><i class="mdi mdi-cart-outline"></i></div>
                                <p class="notify-details">Your order is placed<small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="notify-icon bg-warning"><i class="mdi mdi-message"></i></div>
                                <p class="notify-details">New Message received<small class="text-muted">You have 87 unread messages</small></p>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="notify-icon bg-info"><i class="mdi mdi-glass-cocktail"></i></div>
                                <p class="notify-details">Your item is shipped<small class="text-muted">It is a long established fact that a reader will</small></p>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>
                                <p class="notify-details">Your order is placed<small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <div class="notify-icon bg-danger"><i class="mdi mdi-message"></i></div>
                                <p class="notify-details">New Message received<small class="text-muted">You have 87 unread messages</small></p>
                            </a>
                        </div>
                        <!-- All-->
                        <a href="javascript:void(0);" class="dropdown-item text-center text-primary">
                            View all <i class="fi-arrow-right"></i>
                        </a>
                    </div>
                </li>
                <?php endif; ?>

                <li class="dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <img src="<?= base_url('public/assets/images/users/'.((session()->get('log_id') == 1) ? "admin" : "user").'.jpg'); ?>" alt="profile-user" class="rounded-circle" /> 
                        <span class="ml-1 nav-user-name hidden-sm"> <i class="mdi mdi-chevron-down"></i> </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php if($_dadosAtalho): ?>
                            <?php foreach($_dadosAtalho as $keya => $Atalho): ?>
                                <a class="dropdown-item" href="<?= base_url($Atalho['caminho']); ?>"><?= $Atalho['icone']; ?> <?= $Atalho['menutit']; ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a class="dropdown-item" href="<?= base_url('portal/alterasenha'); ?>"><i class="mdi mdi-key text-muted mr-2"></i> Alterar Senha</a>


                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('acesso/logout'); ?>"><i class="dripicons-exit text-muted mr-2"></i> Desconectar</a>
                    </div>
                </li>
                <?php if(!MENU_VERTICAL): ?>
                    <li class="menu-item">
                        <!-- Mobile menu toggle-->
                        <a class="navbar-toggle nav-link" id="mobileToggle">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="list-unstyled topbar-nav mb-0">
                
                <?php if(MENU_VERTICAL): ?>
                    <li>
                        <button class="button-menu-mobile nav-link waves-effect waves-light">
                            <i class="mdi mdi-menu nav-icon"></i>
                        </button>
                    </li>
                <?php endif; ?>

                <li class="hide-phone hidden-sm">
                    <div class="btn-group mb-2 mb-md-0 mt-3">
                        <button type="button" class="btn btn-soft-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-factory"></i> <?= (session()->get('func_coligada')) ? limitaTexto($_dadosColigadaAtiva,15) : "Coligada" ?> <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu">
                            <?php if($_resColigada): ?>
                                <?php foreach($_resColigada as $idC => $_Coligada): ?>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="selecionaColigada('<?= $_Coligada['CODCOLIGADA']; ?>')"><?= $_Coligada['CODCOLIGADA'].' - '.$_Coligada['NOME'].' - '.$_Coligada['NOMEFANTASIA'] ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div><!-- /btn-group -->
                </li>

                <?php if($_resChapa): ?>
                <li class="hide-phone hidden-sm">
                    <div class="btn-group mb-2 mb-md-0 mt-3 ml-2">
                        <button type="button" class="btn btn-soft-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-alt"></i> <?= (session()->get('func_chapa')) ? "Registro Atual [".util_chapa(session()->get('func_chapa'))['CHAPA']."]" : "Registros" ?> <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu">
                            
                                <?php foreach($_resChapa as $idR => $_DadosFuncionario): ?>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="selecionaRegistro('<?= base64_encode($_DadosFuncionario['CHAPA'].':'.$_DadosFuncionario['CODCOLIGADA']); ?>')"><?= $_DadosFuncionario['CHAPA'].' - '.$_DadosFuncionario['NOME']; ?></a>
                                <?php endforeach; ?>
                            
                        </div>
                    </div><!-- /btn-group -->
                </li>
                <?php endif; ?>

                
                
            </ul>

        </nav>
        <!-- end navbar-->
    </div>
    <!-- Top Bar End -->
        <div class="page-wrapper-img noprint">
            <div class="page-wrapper-img-inner">
                <div class="sidebar-user media">                    
                    <img src="<?= base_url('public/assets/images/users/'.((session()->get('log_id') == 1) ? "admin" : "user").'.jpg'); ?>" alt="user" class="rounded-circle img-thumbnail mb-1">
                    <span class="online-icon"><i class="mdi mdi-record text-success"></i></span>
                    <div class="media-body" style="max-width: 188px;">
                        <h5 class="text-light text-left"><?= session()->get('log_nome'); ?></h5>
                        <ul class="list-unstyled list-inline mb-0 mt-2 text-left">
                            <!--
                            <li class="list-inline-item">
                                <a href="#" class=""><i class="mdi mdi-account text-light"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class=""><i class="mdi mdi-settings text-light"></i></a>
                            </li>
                                -->
                            <li class="list-inline-item text-center">
                                <a href="<?= base_url('acesso/logout'); ?>" class=""><i class="mdi mdi-power text-danger"></i></a>
                            </li>
                        </ul>
                    </div>                    
                </div>
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                        <!--
                                <div class="float-right align-item-center mt-2">
                                <button class="btn btn-info px-4 align-self-center report-btn">Create Report</button>
                            </div>
                        -->
                            <h4 class="page-title mb-2"><?= $_moduloName; ?></h4>
                            <?= $_breadcrumb; ?>                                       
                        </div><!--end page title box-->
                    </div><!--end col-->
                </div><!--end row-->
                <!-- end page title end breadcrumb -->
            </div><!--end page-wrapper-img-inner-->
        </div><!--end page-wrapper-img-->

        <div class="page-wrapper">
            <div class="page-wrapper-inner ">


                <?php if(MENU_VERTICAL): ?>
                <!-- Left Sidenav -->
                <div class="left-sidenav noprint">

                    
                    
                    
                    <ul class="metismenu left-sidenav-menu" id="side-nav">

                            <div class="col-12 hidden-lg">

                                
                                <div class="alert alert-secondary pt-1 pb-1 pr-2 pl-2 mb-2 text-center" role="alert">
                                    <?= session()->get('log_nome'); ?>
                                </div>
                                

                                <div class="btn-group btn-block">
                                    <button type="button" class="btn btn-soft-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-factory"></i> <?= (session()->get('func_coligada')) ? limitaTexto($_dadosColigadaAtiva,15) : "Coligada" ?> <i class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu">
                                        <?php if($_resColigada): ?>
                                            <?php foreach($_resColigada as $idC => $_Coligada): ?>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="selecionaColigada('<?= $_Coligada['CODCOLIGADA']; ?>')"><?= limitaTexto($_Coligada['CODCOLIGADA'].' - '.$_Coligada['NOMEFANTASIA'],26) ?></a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div><!-- /btn-group -->
                            

                                <?php if($_resChapa): ?>
                                    <div class="btn-group btn-block">
                                        <button type="button" class="btn btn-soft-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-alt"></i> <?= (session()->get('func_chapa')) ? "Registro Atual [".util_chapa(session()->get('func_chapa'))['CHAPA']."]" : "Registros" ?> <i class="mdi mdi-chevron-down"></i></button>
                                        <div class="dropdown-menu">
                                            
                                                <?php foreach($_resChapa as $idR => $_DadosFuncionario): ?>
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="selecionaRegistro('<?= base64_encode($_DadosFuncionario['CHAPA'].':'.$_DadosFuncionario['CODCOLIGADA']); ?>')"><?= limitaTexto($_DadosFuncionario['CHAPA'],26); ?></a>
                                                <?php endforeach; ?>
                                            
                                        </div>
                                    </div><!-- /btn-group -->
                                <?php endif; ?>
                            </div>

                        <li class="menu-title">Main</li>

                        <?= menu(); ?>

                    </ul>
                </div>
                
                <!-- end left-sidenav-->
                <?php else: ?>
                    <!-- Navbar Custom Menu -->
                    <div class="navbar-custom-menu noprint">
                        
                        <div class="container-fluid">
                            <div id="navigation">
                                <!-- Navigation Menu-->
                                <ul class="navigation-menu list-unstyled">
    
                                    <?= menu(); ?>
                                    
                                </ul>
                                <!-- End navigation menu -->
                            </div> <!-- end navigation -->
                        </div> <!-- end container-fluid -->
                    </div>
                    <!-- end left-sidenav-->
                    </div>
                    <?php endif; ?>
            
                

            <!-- Page Content-->
            <div class="page-content">