<?php
namespace AnamneseEstetica;

defined('ABSPATH') || exit;

/**
 * Classe para gerenciar chamadas AJAX
 */
class Ajax {
    /**
     * Inicializar handlers AJAX
     */
    public static function init() {
        add_action('wp_ajax_delete_anamnese', [self::class, 'deleteAnamnese']);
        add_action('wp_ajax_update_status', [self::class, 'updateStatus']);
        add_action('wp_ajax_get_form_details', [self::class, 'getFormDetails']);
    }

    /**
     * Excluir registro de anamnese
     */
    public static function deleteAnamnese() {
        // Verificar nonce
        if (!check_ajax_referer('anamnese_ajax_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Erro de segurança']);
        }

        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permissão negada']);
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) {
            wp_send_json_error(['message' => 'ID inválido']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . Constants::TABLE_NAME;
        
        // Excluir registro
        $result = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );

        if ($result === false) {
            wp_send_json_error(['message' => 'Erro ao excluir registro']);
        }

        wp_send_json_success(['message' => 'Registro excluído com sucesso']);
    }

    /**
     * Atualizar status do registro
     */
    public static function updateStatus() {
        if (!check_ajax_referer('anamnese_ajax_nonce', 'nonce', false)) {
            wp_send_json_error();
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (!$id || !$status) {
            wp_send_json_error();
        }

        global $wpdb;
        $table_name = $wpdb->prefix . Constants::TABLE_NAME;
        
        $result = $wpdb->update(
            $table_name,
            ['status' => $status],
            ['id' => $id],
            ['%s'],
            ['%d']
        );

        if ($result === false) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }

    /**
     * Obter detalhes do formulário
     */
    public static function getFormDetails() {
        if (!check_ajax_referer('anamnese_ajax_nonce', 'nonce', false)) {
            wp_send_json_error();
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) {
            wp_send_json_error();
        }

        $db = new Database();
        $result = $db->getFormDetails($id);

        if (!$result) {
            wp_send_json_error();
        }

        wp_send_json_success(['data' => $result]);
    }
}
