<?php
/**
 * Add support for Multi Store
 * @since 2.8
**/

global $store_id, $CurrentStore;

$option_prefix		=	'';

if( $store_id != null ) {
	$option_prefix	=	'store_' . $store_id . '_' ;
}

$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_discount_customers',
    'title'            =>        __('Réglages de la caisse', 'nexo'),
    'col_id'        =>        1,
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    ),
    'use_namespace'    =>        false,
));

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'enable_group_discount',
    'label'        =>    __('Activer les remises de groupe', 'nexo'),
    'options'    =>    array(
        'disable'    =>    __('Désactiver', 'nexo'),
        'enable'    =>    __('Activer', 'nexo')
    )
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'discount_type',
    'label'        =>    __('Type de la remise', 'nexo'),
    'options'    =>    array(
        'disable'    =>    __('Désactiver', 'nexo'),
        'percent'    =>    __('Au pourcentage', 'nexo'),
        'amount'    =>    __('Montant fixe', 'nexo'),
    )
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'how_many_before_discount',
    'label'        =>    __('Reduction Automatique', 'nexo'),
    'description'    =>    __("Après combien de commandes un client peut-il profiter d'une remise automatique. Veuillez définir une valeur numérique. \"0\" désactive la fonctionnalité.", 'nexo')
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'discount_percent',
    'label'        =>    __('Pourcentage de la remise', 'nexo')
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'discount_amount',
    'label'        =>    __('Montant fixe', 'nexo')
), 'Nexo_discount_customers', 1);

/** 
 * Fetch Clients
**/

$this->load->model( 'Nexo_Misc' );
$result			=	get_instance()->Nexo_Misc->get_customers();
$options        =    array();

foreach ($result as $_r) {
    $options[ $_r[ 'ID' ] ]        =    $_r[ 'NOM' ];
}

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'default_compte_client',
    'label'        =>    __('Compte Client par défaut', 'nexo'),
    'description'    =>    __('Ce client ne profitera pas des réductions automatique.', 'nexo'),
    'options'    =>    $options
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_reward_system',
    'label'        =>    __('Activer le système des récompenses', 'nexo'),
    'description'    =>    __('Les clients pourront beneficier de coupons de réductions sur la base de leurs achats.', 'nexo'),
    'options'    =>    [
        ''   =>  __( 'Veuillez choisir une option', 'nexo' ),
        'yes'   =>  __( 'Oui', 'nexo' ),
        'no'   =>  __( 'Non', 'nexo' ),
    ]
), 'Nexo_discount_customers', 1);

$this->Gui->add_meta([
    'namespace'         =>        'customers_account',
    'title'             =>        __( 'Réglages de la caisse', 'nexo'),
    'col_id'            =>        2,
    'gui_saver'         =>        true,
    'footer'            =>        array(
        'submit'        =>        array(
            'label'     =>        __('Sauvegarder les réglages', 'nexo')
        )
    ),
    'use_namespace'    =>        false,
]);

$this->Gui->add_item([
    'type'              =>    'select',
    'name'              =>    $option_prefix . 'enable_customers_accounts',
    'label'             =>    __('Activer les Comptes & Historique', 'nexo'),
    'options'           =>    [
        ''                  =>    __( 'Choisissez une valeur', 'nexo' ),
        'yes'               =>    __('Oui', 'nexo'),
        'no'                =>    __('Non', 'nexo')
    ],
    'description'       =>  __( 'Permet d\'activer les comptes & l\'historique des comptes pour les clients', 'nexo' )
], 'customers_account', 2 );

$this->Gui->add_item([
    'type'              =>    'select',
    'name'              =>    $option_prefix . 'allow_negative_credit',
    'label'             =>    __('Autoriser le crédit due', 'nexo'),
    'options'           =>    [
        ''                  =>    __( 'Choisissez une valeur', 'nexo' ),
        'yes'               =>    __('Oui', 'nexo'),
        'no'                =>    __('Non', 'nexo')
    ],
    'description'       =>  __( 'Le client pourra obtenir un crédit, même si son compte ne possède aucun crédit', 'nexo' )
], 'customers_account', 2 );

$this->Gui->add_item([
    'type'              =>    'select',
    'name'              =>    $option_prefix . 'customer_requested_prior_payment',
    'label'             =>    __('Paiment Imposée Par Commandes', 'nexo'),
    'options'           =>    [
        'disable'               =>    __('Désactiver', 'nexo'),
        '10'                    =>    __('10%', 'nexo'),
        '15'                    =>    __('15%', 'nexo'),
        '20'                    =>    __('20%', 'nexo'),
        '25'                    =>    __('25%', 'nexo'),
        '30'                    =>    __('30%', 'nexo'),
        '35'                    =>    __('35%', 'nexo'),
        '40'                    =>    __('40%', 'nexo'),
        '45'                    =>    __('45%', 'nexo'),
        '50'                    =>    __('50%', 'nexo'),
        '55'                    =>    __('55%', 'nexo'),
        '60'                    =>    __('60%', 'nexo'),
        '65'                    =>    __('65%', 'nexo'),
        '70'                    =>    __('70%', 'nexo'),
        '75'                    =>    __('75%', 'nexo'),
        '80'                    =>    __('80%', 'nexo'),
        '85'                    =>    __('85%', 'nexo'),
        '90'                    =>    __('90%', 'nexo'),
        '95'                    =>    __('95%', 'nexo'),
    ],
    'description'       =>  __( 'Exiger le paiement d\'une partie de la commande pour autoriser le paiement à crédit.', 'nexo' )
], 'customers_account', 2 );

$this->Gui->output();
