<?php
/*
Plugin Name: Anamnese Estetica
Plugin URI: https://www.instagram.com/walther_aa
Description: Sistema de gerenciamento de anamnese para clinica de estetica
Version: 1.0
Author: Walther Alves Almeida
Author URI: https://www.instagram.com/walther_aa
*/

if (!defined('ABSPATH')) {
    exit;
}

// Create database table on plugin activation
register_activation_hook(__FILE__, 'anamnese_create_table');
function anamnese_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'anamnese_forms';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome varchar(100) NOT NULL,
        data_nascimento date NOT NULL,
        idade int NOT NULL,
        celular varchar(20) NOT NULL,
        objetivo text,
        dor text,
        pressao varchar(10),
        diabetes varchar(10),
        alergia text,
        sintomas text,
        intestino varchar(20),
        alimentacao varchar(20),
        anticoncepcional varchar(10),
        gestante varchar(10),
        meses int,
        estresse varchar(10),
        disturbio_renal varchar(10),
        disturbio_renal_desc text,
        enxaqueca varchar(10),
        antecedentes_oncologicos varchar(10),
        antecedentes_oncologicos_desc text,
        saude_relevante text,
        depressao varchar(10),
        insonia varchar(10),
        pedras_rins varchar(10),
        pedras_vesicula varchar(10),
        dor_mandibula varchar(10),
        bruxismo varchar(10),
        lentes_contato varchar(10),
        doenca varchar(10),
        doenca_desc text,
        nao_gosta varchar(10),
        nao_gosta_detalhes text,
        assinatura_cliente varchar(100),
        assinatura_profissional varchar(100),
        data_criacao datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'não visto',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Função para atualizar a tabela
function atualizar_tabela_anamnese() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'anamnese_forms';
    $charset_collate = $wpdb->get_charset_collate();

    // Lista de colunas a serem verificadas/adicionadas
    $colunas = array(
        'disturbio_renal' => 'varchar(10)',
        'disturbio_renal_desc' => 'text',
        'enxaqueca' => 'varchar(10)',
        'antecedentes_oncologicos' => 'varchar(10)',
        'antecedentes_oncologicos_desc' => 'text',
        'saude_relevante' => 'text',
        'depressao' => 'varchar(10)',
        'insonia' => 'varchar(10)',
        'pedras_rins' => 'varchar(10)',
        'pedras_vesicula' => 'varchar(10)',
        'dor_mandibula' => 'varchar(10)',
        'bruxismo' => 'varchar(10)',
        'lentes_contato' => 'varchar(10)',
        'doenca' => 'varchar(10)',
        'doenca_desc' => 'text',
        'nao_gosta' => 'varchar(10)',
        'nao_gosta_detalhes' => 'text'
    );

    // Verificar cada coluna e adicionar se não existir
    foreach ($colunas as $coluna => $tipo) {
        $coluna_existe = $wpdb->get_results("SHOW COLUMNS FROM `{$table_name}` LIKE '{$coluna}'");
        if (empty($coluna_existe)) {
            $wpdb->query("ALTER TABLE `{$table_name}` ADD `{$coluna}` {$tipo}");
        }
    }
}

// Registrar função para ser executada na ativação do plugin
register_activation_hook(__FILE__, 'anamnese_create_table');
register_activation_hook(__FILE__, 'atualizar_tabela_anamnese');

// Executar atualização manualmente para tabelas existentes
add_action('init', 'atualizar_tabela_anamnese');

// Carregar classes
require_once plugin_dir_path(__FILE__) . 'includes/constants.php';
require_once plugin_dir_path(__FILE__) . 'includes/Database.php';
require_once plugin_dir_path(__FILE__) . 'includes/FormValidator.php';
require_once plugin_dir_path(__FILE__) . 'includes/Assets.php';
require_once plugin_dir_path(__FILE__) . 'includes/Ajax.php';

// Inicializar classes
add_action('init', ['AnamneseEstetica\\Assets', 'init']);
add_action('init', ['AnamneseEstetica\\Ajax', 'init']);

// Add admin menu
add_action('admin_menu', 'anamnese_admin_menu');
function anamnese_admin_menu() {
    add_menu_page(
        'Anamnese',
        'Anamnese',
        'manage_options',
        'anamnese-lista',
        'anamnese_lista_page',
        'dashicons-clipboard',
        30
    );
}

// Process form submission
add_action('init', 'process_anamnese_form');
function process_anamnese_form() {
    error_log('Verificando submissão do formulário...');
    
    if (!empty($_POST)) {
        error_log('POST não está vazio. Conteúdo: ' . print_r($_POST, true));
    }
    
    if (isset($_POST['anamnese_submit'])) {
        error_log('Formulário recebido - anamnese_submit presente');
        
        if (!isset($_POST['anamnese_form_nonce'])) {
            error_log('Nonce não encontrado');
            wp_die('Erro de segurança: nonce não encontrado');
        }
        
        if (!wp_verify_nonce($_POST['anamnese_form_nonce'], 'anamnese_form_action')) {
            error_log('Falha na verificação do nonce');
            wp_die('Erro de segurança: verificação do nonce falhou');
        }

        error_log('Nonce verificado com sucesso');

        global $wpdb;
        $table_name = $wpdb->prefix . 'anamnese_forms';

        try {
            $data = array(
                'nome' => sanitize_text_field($_POST['nome']),
                'data_nascimento' => sanitize_text_field($_POST['data-nascimento']),
                'idade' => intval($_POST['idade']),
                'celular' => sanitize_text_field($_POST['celular']),
                'objetivo' => sanitize_textarea_field($_POST['objetivo']),
                'dor' => sanitize_textarea_field($_POST['dor']),
                'pressao' => sanitize_text_field($_POST['pressao']),
                'diabetes' => sanitize_text_field($_POST['diabetes']),
                'alergia' => sanitize_textarea_field($_POST['alergia']),
                'sintomas' => sanitize_textarea_field($_POST['sintomas']),
                'intestino' => sanitize_text_field($_POST['intestino']),
                'alimentacao' => sanitize_text_field($_POST['alimentacao']),
                'anticoncepcional' => sanitize_text_field($_POST['anticoncepcional']),
                'gestante' => sanitize_text_field($_POST['gestante']),
                'meses' => isset($_POST['meses']) ? intval($_POST['meses']) : null,
                'estresse' => sanitize_text_field($_POST['estresse']),
                'disturbio_renal' => sanitize_text_field($_POST['disturbio-renal']),
                'disturbio_renal_desc' => sanitize_textarea_field($_POST['disturbio-renal-desc']),
                'enxaqueca' => sanitize_text_field($_POST['enxaqueca']),
                'antecedentes_oncologicos' => sanitize_text_field($_POST['antecedentes-oncologicos']),
                'antecedentes_oncologicos_desc' => sanitize_textarea_field($_POST['antecedentes-oncologicos-desc']),
                'saude_relevante' => sanitize_textarea_field($_POST['saude-relevante']),
                'depressao' => sanitize_text_field($_POST['depressao']),
                'insonia' => sanitize_text_field($_POST['insonia']),
                'pedras_rins' => sanitize_text_field($_POST['pedras-rins']),
                'pedras_vesicula' => sanitize_text_field($_POST['pedras-vesicula']),
                'dor_mandibula' => sanitize_text_field($_POST['dor-mandibula']),
                'bruxismo' => sanitize_text_field($_POST['bruxismo']),
                'lentes_contato' => sanitize_text_field($_POST['lentes-contato']),
                'doenca' => sanitize_text_field($_POST['doenca']),
                'doenca_desc' => sanitize_textarea_field($_POST['doenca-desc']),
                'nao_gosta' => sanitize_text_field($_POST['nao_gosta']),
                'nao_gosta_detalhes' => sanitize_textarea_field($_POST['nao_gosta_detalhes']),
                'assinatura_cliente' => sanitize_text_field($_POST['assinatura-cliente']),
                'assinatura_profissional' => sanitize_text_field($_POST['assinatura-profissional'])
            );

            error_log('Dados preparados para inserção: ' . print_r($data, true));

            $result = $wpdb->insert($table_name, $data);
            
            if ($result === false) {
                error_log('Erro ao inserir no banco: ' . $wpdb->last_error);
                throw new Exception('Erro ao salvar os dados: ' . $wpdb->last_error);
            }

            error_log('Dados inseridos com sucesso. ID: ' . $wpdb->insert_id);
            
            $redirect_url = add_query_arg('status', 'success', $_SERVER['REQUEST_URI']);
            error_log('Redirecionando para: ' . $redirect_url);
            
            wp_redirect($redirect_url);
            exit;

        } catch (Exception $e) {
            error_log('Exceção capturada: ' . $e->getMessage());
            wp_die('Erro ao processar o formulário: ' . esc_html($e->getMessage()));
        }
    }
}

// Shortcode to display the form
add_shortcode('anamnese_form', 'anamnese_form_shortcode');
function anamnese_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form.php';
    return ob_get_clean();
}

// Admin page display function
function anamnese_lista_page() {
    include plugin_dir_path(__FILE__) . 'admin/lista.php';
}

// Add plugin styles
add_action('wp_enqueue_scripts', 'anamnese_enqueue_styles');
add_action('admin_enqueue_scripts', 'anamnese_enqueue_styles');
function anamnese_enqueue_styles() {
    wp_enqueue_style('anamnese-style', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('anamnese-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), '1.0', true);
}

// AJAX handlers for admin actions
add_action('wp_ajax_update_status', 'update_anamnese_status');
function update_anamnese_status() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'anamnese_forms';
    $id = intval($_POST['id']);
    $status = sanitize_text_field($_POST['status']);

    $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $id)
    );

    wp_send_json_success();
}

// AJAX handler para buscar detalhes do formulário
add_action('wp_ajax_get_form_details', 'get_anamnese_form_details');
function get_anamnese_form_details() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'anamnese_forms';
    $id = intval($_POST['id']);

    $form = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id),
        ARRAY_A
    );

    if ($form) {
        wp_send_json_success($form);
    } else {
        wp_send_json_error('Formulário não encontrado');
    }
}

// Export to CSV
add_action('admin_post_export_anamnese_csv', 'export_anamnese_to_csv');
function export_anamnese_to_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'anamnese_forms';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY data_criacao DESC", ARRAY_A);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="anamnese-export-' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($results[0]));

    foreach ($results as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
