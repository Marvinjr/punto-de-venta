<?php
/**
 * New print solution
 * )@since 3.12.5
 */
$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    '<h4>' . __('Configuration de l\'impression', 'nexo') . '</h4>'
), $namespace, 1);

/**
 * @since 2.3
**/
$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    store_prefix() . 'nexo_enable_autoprint',
    'label'        =>    __('Activer l\'impression automatique des tickets de caisse ?', 'nexo'),
    'description'        =>    __('Par défaut vaut : "Non"', 'nexo'),
    'options'    =>    array(
        ''            =>    __('Veuillez choisir une option', 'nexo'),
        'yes'        =>    __('Oui', 'nexo'),
        'no'        =>    __('Non', 'nexo')
    )
), $namespace, 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    store_prefix() . 'nexo_print_gateway',
    'label'                 =>  __( 'Passerelle d\'impression ?', 'nexo'),
    'description'           =>  __( 'Par défaut vaut : "Impression normale". Vous pouvez aussi décider d\'utiliser les imprimantes des caisses enregistreuses.', 'nexo'),
    'options'               =>  $this->events->apply_filters( 'nexopos_print_gateway', array(
        ''                  =>  __('Veuillez choisir une option', 'nexo'),
        'normal_print'      =>  __('Impression Normale', 'nexo'),
        'nexo_print_server' =>  __('Nexo Print Server', 'nexo'),
        'register_nps'      =>  __( 'Imprimantes des caisses enregistreuses (NPS)', 'nexo' )
    ) )
), $namespace, 1);

if ( store_option( 'nexo_print_gateway' ) === 'nexo_print_server' ) {
    $this->Gui->add_item(array(
        'type'          =>    'text',
        'label'         =>    __('Nexo Print Server URL', 'nexo'),
        'name'          =>    store_prefix() . 'nexo_print_server_url',
        'description'   =>    __('Par défaut: "http://localhost:3236"', 'nexo')
    ), $namespace, 1);

    $this->Gui->add_item(array(
        'type'          =>    'select',
        'name'          =>    store_prefix() . 'nexo_pos_printer',
        'label'         =>    __( 'Choisir une imprimante', 'nexo'),
        'description'   =>    __('Choisir une imprimante pour les tickets de caisse', 'nexo'),
        'options'       =>    array(
            ''          =>    __('Veuillez choisir une option', 'nexo')
        )
    ), $namespace, 1);

    $this->Gui->add_item(array(
        'type'        =>    'dom',
        'content'    =>     $this->load->module_view( 'nexo', 'settings.select-printer-script', null, true )
    ), $namespace, 1);

    $this->Gui->add_item(array(
        'type'        =>    'select',
        'name'        =>    store_prefix() . 'nexo_nps_print_copies',
        'label'        =>    __( 'Nombre d\'exemplaire d\'impression NPS', 'nexo'),
        'description'   =>  __( 'Permet de déterminer le nombre de copie à imprimer sur Nexo Print Server.', 'nexo' ),
        'options'    =>    [
            1   =>  __( '1 Copie', 'nexo' ),
            2   =>  __( '2 Copies', 'nexo' ),
            3   =>  __( '3 Copies', 'nexo' ),
            4   =>  __( '4 Copies', 'nexo' ),
            5   =>  __( '5 Copies', 'nexo' )
        ]
    ), $namespace, 1);

    $this->Gui->add_item(array(
        'type'        =>    'select',
        'name'        =>    store_prefix() . 'nexo_use_cashrawer',
        'label'        =>    __('Activer le tiroir de caisse ?', 'nexo'),
        'description'        =>    __('Si votre point de vente dispose d\'une caisse, activez cette option.', 'nexo'),
        'options'    =>    array(
            ''            =>    __('Veuillez choisir une option', 'nexo'),
            'yes'        =>    __('Oui', 'nexo'),
            'no'        =>    __('Non', 'nexo')
        )
    ), $namespace, 1);

    $this->Gui->add_item( array(
        'type'			=>	'text',
        'name'			=>	store_prefix() . 'nps_width',
        'label'			=>	__( 'Lettre par ligne', 'nexo' ),
        'description'	=>	__( 'La longueur de chaque ticket varie en fonction de l\'appareil utilisé. 
        Nexo Print Server vous permet de définir une largeur ajustable, qui permettra au contenu de s\'adapter au ticket de caisse. Par défault : 48', 'nexo' )
    ), $namespace, 1 );

    $this->Gui->add_item( array(
        'type'			=>	'select',
        'name'			=>	store_prefix() . 'nps_max_footer_space',
        'label'			=>	__( 'Espace au pied de page', 'nexo', 'nexo' ),
        'options'		=>	[
            '0'		=>	__( 'Aucun espace', 'nexo' ),
            '1'		=>	__( '1', 'nexo' ),
            '2'		=>	__( '2', 'nexo' ),
            '3'		=>	__( '3', 'nexo' ),
            '4'		=>	__( '4', 'nexo' ),
            '5'		=>	__( '5', 'nexo' )
        ],
        'description'	=>	__( 'Vous permet de définir un espace au pied de page sur NPS.', 'nexo' )
    ), $namespace, 1 );

    $this->Gui->add_item( array(
        'type'			=>	'select',
        'name'			=>	store_prefix() . 'nps_logo_type',
        'label'			=>	__( 'Type du logo', 'nexo' ),
        'options'		=>	[
            'nps-logo'	=>	__( 'Logo NPS', 'nexo' ),
            'store-name'	=>	__( 'Nom de la boutique', 'nexo' )
        ],
        'description'	=>	__( 'Vous permet de choisir d\'utiliser le logo crée sur Nexo Print Server 2.x ou d\'utiliser le nom de la boutique.', 'nexo' )
    ), $namespace, 1 );

    $this->Gui->add_item( array(
        'type'			=>	'text',
        'name'			=>	store_prefix() . 'nps_logo',
        'label'			=>	__( 'Code Court', 'nexo' ),
        'description'	=>	__( 'Le code court est l\'identifiant d\'un logo tel qu\'il est défini sur Nexo Print Server 2.x.', 'nexo' )
    ), $namespace, 1 );

    /**
     * @todo might be added to add
     * a base64 print based
     */
    $this->Gui->add_item( array(
        'type'			=>	'select',
        'name'			=>	store_prefix() . 'nps_print_base64',
        'options'		=>	[
            'no'		=>	__( 'Non', 'nexo' ),
            'yes'		=>	__( 'Oui', 'nexo' ),
        ],
        'label'			=>	__( 'Convertir Impression en Image', 'nexo' ),
        'description'	=>	__( 'Convertir une impression en image vous permet d\'améliorer la compatibilité avec d\'autres langues. Cependant, l\'impression peut être unpeu lente.', 'nexo' )
    ), $namespace, 1 );

} else if ( store_option( 'nexo_print_gateway' ) === 'normal_print' ) {
    $this->Gui->add_item(array(
        'type'        =>    'dom',
        'content'    =>    '<h4>' . __( 'Configuration du thème des reçus', 'nexo') . '</h4>'
    ), $namespace, 1 );
    
    $receipt_themes 	=	$this->events->apply_filters( 'nexo_receipt_theme', array(
        'default'       =>  __('Par défaut', 'nexo'),
        'light'		    =>	__( 'Léger', 'nexo' ),
        'simple'		=>	__( 'Simple', 'nexo' )
    ) );
    
    $this->Gui->add_item(array(
        'type'        =>    'select',
        'name'        =>    store_prefix() . 'nexo_receipt_theme',
        'label'        =>    __('Thème des tickets de caisse', 'nexo'),
        'options'    =>    $receipt_themes
    ), $namespace, 1 );
}

$this->events->do_action( 'nexopos_print_settings_gui', $this->Gui, $namespace );
