<?php
namespace AnamneseEstetica;

defined('ABSPATH') || exit;

/**
 * Classe responsável por gerenciar operações de banco de dados
 */
class Database {
    private $wpdb;
    private $table_name;

    /**
     * Construtor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . Constants::TABLE_NAME;
    }

    /**
     * Criar tabela do plugin
     */
    public function createTable() {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
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
            status varchar(20) DEFAULT '" . Constants::STATUS['NAO_VISTO'] . "',
            PRIMARY KEY  (id),
            KEY idx_nome (nome),
            KEY idx_data_criacao (data_criacao),
            KEY idx_status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Inserir novo registro
     * 
     * @param array $data Dados a serem inseridos
     * @return int|false ID do registro inserido ou false em caso de erro
     */
    public function insert($data) {
        $result = $this->wpdb->insert($this->table_name, $data);
        if ($result === false) {
            error_log('Erro ao inserir anamnese: ' . $this->wpdb->last_error);
            return false;
        }
        return $this->wpdb->insert_id;
    }

    /**
     * Atualizar registro
     * 
     * @param int $id ID do registro
     * @param array $data Dados a serem atualizados
     * @return bool
     */
    public function update($id, $data) {
        $result = $this->wpdb->update(
            $this->table_name,
            $data,
            ['id' => $id]
        );
        return $result !== false;
    }

    /**
     * Buscar registros com paginação e filtros
     * 
     * @param array $args Argumentos de busca
     * @return array
     */
    public function getRecords($args = []) {
        $defaults = [
            'per_page' => 20,
            'page' => 1,
            'search' => '',
            'status' => '',
            'orderby' => 'data_criacao',
            'order' => 'DESC'
        ];

        $args = wp_parse_args($args, $defaults);
        $where = [];
        $values = [];

        if (!empty($args['search'])) {
            $where[] = 'nome LIKE %s';
            $values[] = '%' . $this->wpdb->esc_like($args['search']) . '%';
        }

        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($args['page'] - 1) * $args['per_page'];

        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            {$where_clause}
            ORDER BY {$args['orderby']} {$args['order']}
            LIMIT %d OFFSET %d",
            array_merge($values, [$args['per_page'], $offset])
        );

        return [
            'items' => $this->wpdb->get_results($query),
            'total' => $this->getTotalRecords($where, $values),
            'total_pages' => ceil($this->getTotalRecords($where, $values) / $args['per_page'])
        ];
    }

    /**
     * Obter total de registros
     * 
     * @param array $where Condições WHERE
     * @param array $values Valores para prepared statement
     * @return int
     */
    private function getTotalRecords($where = [], $values = []) {
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $query = "SELECT COUNT(*) FROM {$this->table_name} {$where_clause}";
        if (!empty($values)) {
            $query = $this->wpdb->prepare($query, $values);
        }

        return (int) $this->wpdb->get_var($query);
    }

    /**
     * Excluir registros antigos
     * 
     * @param int $days Número de dias para manter
     * @return int Número de registros excluídos
     */
    public function cleanOldRecords($days = 365) {
        return $this->wpdb->query($this->wpdb->prepare(
            "DELETE FROM {$this->table_name} WHERE data_criacao < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }

    /**
     * Obter detalhes do formulário
     * 
     * @param int $id ID do registro
     * @return object|null
     */
    public function getFormDetails($id) {
        $sql = $this->wpdb->prepare(
            "SELECT 
                id,
                nome,
                DATE(data_nascimento) as data_nascimento,
                idade,
                celular,
                objetivo,
                dor,
                pressao,
                diabetes,
                alergia,
                sintomas,
                intestino,
                alimentacao,
                anticoncepcional,
                gestante,
                meses,
                estresse,
                disturbio_renal,
                disturbio_renal_desc,
                enxaqueca,
                antecedentes_oncologicos,
                antecedentes_oncologicos_desc,
                saude_relevante,
                depressao,
                insonia,
                pedras_rins,
                pedras_vesicula,
                dor_mandibula,
                bruxismo,
                lentes_contato,
                doenca,
                doenca_desc,
                nao_gosta,
                nao_gosta_detalhes,
                assinatura_cliente,
                assinatura_profissional,
                data_criacao,
                status
            FROM {$this->table_name} 
            WHERE id = %d",
            $id
        );
        
        return $this->wpdb->get_row($sql);
    }
}
