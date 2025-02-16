<?php
namespace AnamneseEstetica;

defined('ABSPATH') || exit;

/**
 * Classe responsável por gerenciar os assets do plugin
 */
class Assets {
    /**
     * Registrar e enfileirar assets
     */
    public static function init() {
        add_action('wp_enqueue_scripts', [self::class, 'enqueuePublic']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdmin']);
    }

    /**
     * Enfileirar assets públicos
     */
    public static function enqueuePublic() {
        wp_enqueue_style(
            'anamnese-estetica',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/style.css',
            [],
            Constants::VERSION
        );

        wp_enqueue_script(
            'anamnese-estetica',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/script.js',
            ['jquery'],
            Constants::VERSION,
            true
        );

        wp_localize_script('anamnese-estetica', 'anamneseEstetica', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anamnese_ajax_nonce')
        ]);
    }

    /**
     * Enfileirar assets do admin
     * 
     * @param string $hook_suffix Sufixo da página atual
     */
    public static function enqueueAdmin($hook_suffix) {
        // Carregar apenas nas páginas do plugin
        if (strpos($hook_suffix, 'anamnese') === false) {
            return;
        }

        wp_enqueue_style(
            'anamnese-estetica-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/style.css',
            [],
            Constants::VERSION
        );

        wp_enqueue_script(
            'anamnese-estetica-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/script.js',
            ['jquery', 'wp-util'],
            Constants::VERSION,
            true
        );

        // Adicionar variáveis localizadas
        wp_localize_script('anamnese-estetica-admin', 'anamneseEstetica', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('anamnese_ajax_nonce'),
            'i18n' => [
                'confirmDelete' => __('Tem certeza que deseja excluir este registro?', 'anamnese-estetica'),
                'errorLoading' => __('Erro ao carregar os dados', 'anamnese-estetica'),
                'saved' => __('Salvo com sucesso!', 'anamnese-estetica'),
                'error' => __('Erro ao salvar', 'anamnese-estetica')
            ]
        ]);
    }

    /**
     * Minificar CSS
     * 
     * @param string $css Código CSS
     * @return string CSS minificado
     */
    private static function minifyCss($css) {
        // Remover comentários
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remover espaços após caracteres
        $css = str_replace([': ', ' {', '{ ', ', ', '} ', ' }', ';}'], [':', '{', '{', ',', '}', '}', '}'], $css);
        
        // Remover quebras de linha e tabs
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        
        return trim($css);
    }

    /**
     * Minificar JavaScript
     * 
     * @param string $js Código JavaScript
     * @return string JavaScript minificado
     */
    private static function minifyJs($js) {
        // Remover comentários de linha única
        $js = preg_replace('!^[ \t]*//.+$!m', '', $js);
        
        // Remover comentários multi-linha
        $js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $js);
        
        // Remover espaços múltiplos
        $js = preg_replace('/\s+/', ' ', $js);
        
        return trim($js);
    }
}
