<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'anamnese_forms';

// Handle filters
$where = array();
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
if ($search) {
    $where[] = $wpdb->prepare("nome LIKE %s", '%' . $wpdb->esc_like($search) . '%');
}

$status = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
if ($status) {
    $where[] = $wpdb->prepare("status = %s", $status);
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get paginated results
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name $where_clause ORDER BY data_criacao DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ),
    ARRAY_A
);

$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where_clause");
$total_pages = ceil($total_items / $per_page);
?>

<div class="wrap backend-main">
    <h1>Gerenciamento de Anamnese</h1>

    <!-- Filters -->
    <div class="tablenav top">
        <form method="get" class="alignleft actions">
            <input type="hidden" name="page" value="anamnese-lista">
            
            <input type="search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Buscar por nome...">
            
            <select name="status_filter">
                <option value="">Todos os status</option>
                <option value="não visto" <?php selected($status, 'não visto'); ?>>Não visto</option>
                <option value="visto" <?php selected($status, 'visto'); ?>>Visto</option>
            </select>

            <input type="submit" class="button" value="Filtrar">
        </form>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="alignright">
            <input type="hidden" name="action" value="export_anamnese_csv">
            <?php wp_nonce_field('export_anamnese_csv', 'export_nonce'); ?>
            <input type="submit" class="button" value="Exportar CSV">
        </form>

        <div class="clear"></div>
    </div>

    <!-- Results Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Data de Nascimento</th>
                <th>Celular</th>
                <th>Status</th>
                <th>Data de Criação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo esc_html($row['nome']); ?></td>
                    <td><?php echo esc_html(date('d/m/Y', strtotime($row['data_nascimento']))); ?></td>
                    <td><?php echo esc_html($row['celular']); ?></td>
                    <td>
                        <select class="status-select" data-id="<?php echo esc_attr($row['id']); ?>">
                            <option value="não visto" <?php selected($row['status'], 'não visto'); ?>>Não visto</option>
                            <option value="visto" <?php selected($row['status'], 'visto'); ?>>Visto</option>
                        </select>
                    </td>
                    <td><?php echo esc_html(date('d/m/Y H:i', strtotime($row['data_criacao']))); ?></td>
                    <td>
                        <button class="button view-details" data-id="<?php echo esc_attr($row['id']); ?>">Ver</button>
                        <button class="button print-form" data-id="<?php echo esc_attr($row['id']); ?>">Imprimir</button>
                        <button class="delete-button" data-id="<?php echo esc_attr($row['id']); ?>" title="Excluir">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $total_items; ?> itens</span>
                <span class="pagination-links">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                    ?>
                </span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal for viewing details -->
    <div id="anamnese-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-content"></div>
        </div>
    </div>
</div>

<!-- Print Template (hidden) -->
<div id="print-template" style="display: none;">
    <!-- Template content will be populated via JavaScript -->
</div>
