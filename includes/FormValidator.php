<?php
namespace AnamneseEstetica;

defined('ABSPATH') || exit;

/**
 * Classe responsável pela validação de formulários
 */
class FormValidator {
    /**
     * Validar dados do formulário
     * 
     * @param array $data Dados do formulário
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validate($data) {
        $errors = [];

        // Validar campos obrigatórios
        foreach (Constants::FORM_FIELDS as $field => $rules) {
            if ($rules['required'] && empty($data[$field])) {
                $errors[$field] = 'Campo obrigatório';
                continue;
            }

            if (!empty($data[$field])) {
                // Validar comprimento máximo
                if (isset($rules['max_length']) && strlen($data[$field]) > $rules['max_length']) {
                    $errors[$field] = "Máximo de {$rules['max_length']} caracteres";
                }

                // Validar tipo de campo
                switch ($rules['type']) {
                    case 'date':
                        if (!self::isValidDate($data[$field])) {
                            $errors[$field] = 'Data inválida';
                        }
                        break;

                    case 'tel':
                        if (!self::isValidPhone($data[$field])) {
                            $errors[$field] = 'Telefone inválido';
                        }
                        break;

                    case 'int':
                        if (!is_numeric($data[$field])) {
                            $errors[$field] = 'Valor deve ser numérico';
                        }
                        break;
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Sanitizar dados do formulário
     * 
     * @param array $data Dados do formulário
     * @return array Dados sanitizados
     */
    public static function sanitize($data) {
        $sanitized = [];

        foreach (Constants::FORM_FIELDS as $field => $rules) {
            if (!isset($data[$field])) {
                continue;
            }

            switch ($rules['type']) {
                case 'text':
                    $sanitized[$field] = sanitize_text_field($data[$field]);
                    break;

                case 'textarea':
                    $sanitized[$field] = sanitize_textarea_field($data[$field]);
                    break;

                case 'tel':
                    $sanitized[$field] = preg_replace('/[^0-9]/', '', $data[$field]);
                    break;

                case 'int':
                    $sanitized[$field] = intval($data[$field]);
                    break;

                case 'date':
                    $sanitized[$field] = sanitize_text_field($data[$field]);
                    break;

                default:
                    $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }

        return $sanitized;
    }

    /**
     * Verificar se é uma data válida
     * 
     * @param string $date Data no formato Y-m-d
     * @return bool
     */
    private static function isValidDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Verificar se é um telefone válido
     * 
     * @param string $phone Número de telefone
     * @return bool
     */
    private static function isValidPhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }

    /**
     * Verificar rate limit
     * 
     * @param string $ip IP do usuário
     * @return bool
     */
    public static function checkRateLimit($ip) {
        $transient_key = Constants::RATE_LIMIT_TRANSIENT . md5($ip);
        $count = get_transient($transient_key);

        if ($count === false) {
            set_transient($transient_key, 1, HOUR_IN_SECONDS);
            return true;
        }

        if ($count >= Constants::MAX_SUBMISSIONS_PER_HOUR) {
            return false;
        }

        set_transient($transient_key, $count + 1, HOUR_IN_SECONDS);
        return true;
    }
}
