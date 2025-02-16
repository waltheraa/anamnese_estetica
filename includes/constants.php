<?php
/**
 * Arquivo de constantes do plugin
 */

namespace AnamneseEstetica;

defined('ABSPATH') || exit;

class Constants {
    // Versão do plugin
    const VERSION = '1.0.0';
    
    // Nome da tabela
    const TABLE_NAME = 'anamnese_forms';
    
    // Status possíveis
    const STATUS = [
        'NAO_VISTO' => 'não visto',
        'EM_ANALISE' => 'em análise',
        'CONCLUIDO' => 'concluído'
    ];
    
    // Campos do formulário
    const FORM_FIELDS = [
        'nome' => ['type' => 'text', 'required' => true, 'max_length' => 100],
        'data_nascimento' => ['type' => 'date', 'required' => true],
        'idade' => ['type' => 'int', 'required' => true],
        'celular' => ['type' => 'tel', 'required' => true, 'max_length' => 20],
        // ... outros campos
    ];
    
    // Permissões
    const REQUIRED_CAPABILITY = 'manage_options';
    
    // Nonces
    const NONCE_ACTION = 'anamnese_form_action';
    const NONCE_FIELD = 'anamnese_form_nonce';
    
    // Rate limiting
    const MAX_SUBMISSIONS_PER_HOUR = 10;
    const RATE_LIMIT_TRANSIENT = 'anamnese_rate_limit_';
}
