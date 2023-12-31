<?php 

class ATR_Admin {

    private $theme_name;
    private $version;
    private $build_menupage;

    public function __construct( $theme_name, $version ){
    
        $this->theme_name = $theme_name;
        $this->version = $version;
        $this->build_menupage = new ATR_Build_Menupage();
        
    }

    /**
     * Registra los archivos de hojas de estilos del área de administración
     */
    public function enqueue_styles( $hook ) {

        if( $hook != 'toplevel_page_res_options_page' && $hook != 'menu-weko_page_res_submenu_weko'){
            return;
        }

        wp_enqueue_style( 
            $this->theme_name, 
            ATR_DIR_URI . 'admin/css/atr-admin.css', 
            array(), 
            $this->version, 
            'all' 
        );

        wp_enqueue_style( 
            'bootstrap', 
            ATR_DIR_URI . 'helpers/bootstrap-5.1.3/css/bootstrap.min.css', 
            array(), 
            '5.1.3', 
            'all' 
        );

    }

    /**
     * Registra los archivos Javascript del área de administración
     */
    public function enqueue_scripts( $hook ) {

        if( $hook != 'toplevel_page_res_options_page' && $hook != 'menu-weko_page_res_submenu_weko'){
            return;
        }

        wp_enqueue_script( 
            $this->theme_name, 
            ATR_DIR_URI . 'admin/js/atr-admin.js', 
            [ 'jquery' ], 
            $this->version, 
            true 
        );

        wp_enqueue_script( 
            'bootstrap', 
            ATR_DIR_URI . 'helpers/bootstrap-5.1.3/js/bootstrap.min.js', 
            [ 'jquery' ], 
            '5.1.3', 
            true 
        );

        //Marco multimedia
        wp_enqueue_media();

        //Función ajax carrousel
        wp_localize_script(
            $this->theme_name,
            'data_carrousel',
            array(
                'url'           => admin_url('admin-ajax.php'),
                'seguridad'     => wp_create_nonce('weko_data_seg'),
                'objeto'        => get_option('weko_options_carrousel')
            )
        );

        //Función ajax Servicios
        wp_localize_script(
            $this->theme_name,
            'data_servicios',
            array(
                'url'           => admin_url('admin-ajax.php'),
                'seguridad'     => wp_create_nonce('weko_data_seg'),
                'objeto'        => get_option('weko_options_servicios')
            )
        );

    }
    
    /**
     * Registra los menús del theme en el
     * área de administración
     * @version    1.0.0
     * @access   public
     */
    public function add_menu() {

        //Así agregamos el menú
        $this->build_menupage->add_menu_page(
            __( 'Menú Weko', 'weko' ),
            __( 'Menú Weko', 'weko' ),
            'manage_options',
            'res_options_page',
            [ $this, 'controlador_display_menu' ],
            'dashicons-flag',
            15
        );
        

        //Así agregamos el submenu
        $this->build_menupage->add_submenu_page(

            __('res_options_page', 'weko'),
            __('Submenu', 'weko'),
            __('Submenu', 'weko'),
            'manage_options',
            'res_submenu_weko',
            [ $this, 'controlador_display_submenu' ]
        );

        $this->build_menupage->run();

    }

    /**
     * Controla las visualizaciones del menú
     * en el área de administración
     */
    public function controlador_display_menu() {

        $_GET['page_edit']  = (isset($_GET['page_edit']) ? $_GET['page_edit'] : '');
        $_GET['edit']       = (isset($_GET['edit']) ? $_GET['edit'] : '');

        if($_GET['page_edit'] && $_GET['edit']){

            require_once ATR_DIR_PATH . 'admin/partials/atr-menu-weko-display-edit.php';
            
        }else{
            require_once ATR_DIR_PATH . 'admin/partials/atr-menu-weko-display.php';
        }
        
        
        
    }
    public function controlador_display_submenu(){

        require_once ATR_DIR_PATH . 'admin/partials/atr-submenu-weko-display.php';
        
    }

    /**
     * API options Carrousel add
     * function weko carrousel
     */
    public function weko_add_options_carrousel(){

        $imatges = array(
            0 => '/wp-content/themes/storefront/public/images/01.jpg',
            1 => '/wp-content/themes/storefront/public/images/02.jpg',
            2 => '/wp-content/themes/storefront/public/images/03.jpg'
        );

        $id = "weko_options_carrousel";

        if( get_option($id) ){
            return;
        }else{
            add_option( $id, $imatges, true );
        }
    }

    /**
     * API options carrousel update
     * function weko_carrousel
     */
    public function weko_update_options_carrousel(){

        extract( $_POST, EXTR_OVERWRITE );

        //check ajax nonce
        $check_nonce = check_ajax_referer('weko_data_seg', 'nonce');

        if( current_user_can('manage_options') ){

            if( $tipo == 'update' ){

                //update option carrousel
                $objeto_u = update_option("weko_options_carrousel", $objeto_u, true);

                $json = json_encode([
                    'objeto' => $objeto,
                    'objeto_u' => $objeto_u,
                    'check_nonce' => $check_nonce
                ]);

            }

        }

        echo $json;
        wp_die();

    }

    /**
     * API options servicios add
     * function weko_add_options_servicios()
     */
    public function weko_add_options_servicios(){

        $data = array(
            0 => [
                'icon'      => '<i class="fa-solid fa-truck-fast"></li>',
                'title'     => 'Free shipping',
                'content'   => 'in order more €100'
            ],
            1 => [
                'icon'      => '<i class="fa-solid fa-clock"></li>',
                'title'     => '30-days return',
                'content'   => 'Money back guaranted'
            ],
            2 => [
                'icon'      => '<i class="fa-solid fa-comments"></li>',
                'title'     => '24/7 support',
                'content'   => 'Reference clock Support'
            ],
        );

        $id = "weko_options_servicios";

        if(get_option($id)){
            return;
        }else{
            add_option( $id, $data, true );
        }

    }

    /**
     * API options servicios update
     * function weko_servicios()
     */
    public function weko_update_options_servicios(){

        extract( $_POST, EXTR_OVERWRITE );

        //check ajax nonce
        $check_nonce = check_ajax_referer('weko_data_seg', 'nonce');

        if( current_user_can('manage_options') ){

            if( $tipo == 'update' ){

                //update option carrousel
                $update = update_option("weko_options_servicios", $objeto_u, true);

                $json = json_encode([
                    'result' => $update,
                    'objeto_u' => $objeto_u
                ]);

            }

        }

        echo $json;
        wp_die();

    }

}