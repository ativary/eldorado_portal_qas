
            
                    

                    <footer class="footer text-center text-sm-left noprint">
                        &copy; <?= date('Y'); ?> Eldorado - Ariany<span class="text-muted d-none d-sm-inline-block float-right"><i class="mdi mdi-factory text-danger"></i> Coligada: <?= $_dadosColigadaAtiva.' - [<i class="mdi mdi-database text-success"></i> '.DBRM_BANCO.']'; ?></span>
                    </footer>
                </div>
                <!-- end page content -->
            </div>
            <!--end page-wrapper-inner -->
        </div>
        <!-- end page-wrapper -->

        <!-- jQuery  -->
        <script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js').'?v'.VERSION_JS; ?>"></script>
        <?php if(MENU_VERTICAL): ?><script src="<?= base_url('public/assets/js/metisMenu.min.js').'?v'.VERSION_JS; ?>"></script><?php endif; ?>
        <script src="<?= base_url('public/assets/js/waves.min.js').'?v'.VERSION_JS; ?>"></script>
        <script src="<?= base_url('public/assets/js/jquery.slimscroll.min.js').'?v'.VERSION_JS ?>"></script>

        <!-- App js -->
        <script src="<?= base_url('public/assets/js/'.((MENU_VERTICAL) ? 'app' : 'app_h').'.js').'?v'.VERSION_JS; ?>"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="<?= base_url('public/assets/js/custom.js').'?v'.VERSION_JS; ?>"></script>

        <div class="modal noprint" id="modal_loading" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-animation="blur">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <i class="dripicons-loading mdi mdi-spin text-primary" style="font-size: 40px;"></i>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </body>
</html>