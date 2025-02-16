jQuery(document).ready(function($) {
    // Função para ativar modo tela cheia
    function enableFullscreen() {
        $('body').addClass('fullscreen-plugin');
        if (!$('.exit-fullscreen').length) {
            $('<button>')
                .addClass('exit-fullscreen')
                .html('<span class="dashicons dashicons-no-alt"></span> Sair da Tela Cheia')
                .appendTo('body')
                .on('click', disableFullscreen);
        }
        // Remover classes do WordPress que podem interferir
        $('#wpbody-content').removeClass('wrap');
    }

    // Função para desativar modo tela cheia
    function disableFullscreen() {
        $('body').removeClass('fullscreen-plugin');
        $('.exit-fullscreen').remove();
        // Restaurar scroll
        $('html, body').css('overflow', '');
    }

    // Ativar tela cheia se estivermos na página do plugin
    if (window.location.href.indexOf('page=anamnese-estetica') > -1 ||
        window.location.href.indexOf('page=anamnese-lista') > -1) {
        // Pequeno delay para garantir que a página carregou completamente
        setTimeout(enableFullscreen, 100);
    }

    // Calcular idade automaticamente
    $('#data-nascimento').on('change', function() {
        const dataNascimento = new Date(this.value);
        const hoje = new Date();
        
        let idade = hoje.getFullYear() - dataNascimento.getFullYear();
        const mesAtual = hoje.getMonth();
        const mesNascimento = dataNascimento.getMonth();
        
        if (mesAtual < mesNascimento || (mesAtual === mesNascimento && hoje.getDate() < dataNascimento.getDate())) {
            idade--;
        }
        
        $('#idade').val(idade);
    });

    // Controle de campos condicionais
    $('input[name="disturbio-renal"]').change(function() {
        if ($('input[name="disturbio-renal"]:checked').val() === 'sim') {
            $('#disturbio-renal-desc-container').show();
            $('#disturbio-renal-desc').prop('required', true);
        } else {
            $('#disturbio-renal-desc-container').hide();
            $('#disturbio-renal-desc').prop('required', false).val('');
        }
    });

    $('input[name="antecedentes-oncologicos"]').change(function() {
        if ($('input[name="antecedentes-oncologicos"]:checked').val() === 'sim') {
            $('#antecedentes-oncologicos-desc-container').show();
            $('#antecedentes-oncologicos-desc').prop('required', true);
        } else {
            $('#antecedentes-oncologicos-desc-container').hide();
            $('#antecedentes-oncologicos-desc').prop('required', false).val('');
        }
    });

    $('input[name="doenca"]').change(function() {
        if ($('input[name="doenca"]:checked').val() === 'sim') {
            $('#doenca-desc-container').show();
            $('#doenca-desc').prop('required', true);
        } else {
            $('#doenca-desc-container').hide();
            $('#doenca-desc').prop('required', false).val('');
        }
    });

    $('input[name="nao_gosta"]').change(function() {
        if ($('input[name="nao_gosta"]:checked').val() === 'sim') {
            $('#nao_gosta_detalhes_container').show();
            $('#nao_gosta_detalhes').prop('required', true);
        } else {
            $('#nao_gosta_detalhes_container').hide();
            $('#nao_gosta_detalhes').prop('required', false).val('');
        }
    });

    // Esconder campos condicionais inicialmente
    $('#disturbio-renal-desc-container, #antecedentes-oncologicos-desc-container, #doenca-desc-container, #nao_gosta_detalhes_container').hide();

    // Handle gestante radio buttons
    $('input[name="gestante"]').change(function() {
        if ($('#gestante-sim').is(':checked')) {
            $('#meses-container').show();
        } else {
            $('#meses-container').hide();
            $('#meses').val('');
        }
    });

    // Admin page functionality
    if ($('.wp-list-table').length) {
        // Status change handler
        $('.status-select').change(function() {
            const id = $(this).data('id');
            const status = $(this).val();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'update_status',
                    id: id,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert('Status atualizado com sucesso!');
                    }
                }
            });
        });

        // View details handler
        $('.view-details').click(function() {
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_form_details',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        let modalContent = `
                            <div class="anamnese-details">
                                <h2>Detalhes do Formulário de Anamnese</h2>
                                
                                <div class="details-section">
                                    <h3>Dados Pessoais</h3>
                                    <p><strong>Nome:</strong> ${data.nome}</p>
                                    <p><strong>Data de Nascimento:</strong> ${formatDate(data.data_nascimento)}</p>
                                    <p><strong>Idade:</strong> ${data.idade} anos</p>
                                    <p><strong>Celular:</strong> ${data.celular}</p>
                                </div>

                                <div class="details-section">
                                    <h3>Objetivo</h3>
                                    <p><strong>Objetivo com a massagem:</strong> ${data.objetivo}</p>
                                    <p><strong>Dores:</strong> ${data.dor || 'Não relatado'}</p>
                                </div>

                                <div class="details-section">
                                    <h3>Histórico de Saúde</h3>
                                    <p><strong>Pressão Alta sem Medicação:</strong> ${data.pressao}</p>
                                    <p><strong>Diabetes não Controlada:</strong> ${data.diabetes}</p>
                                    <p><strong>Alergias:</strong> ${data.alergia || 'Não relatado'}</p>
                                    <p><strong>Sintomas nas Pernas:</strong> ${data.sintomas || 'Não relatado'}</p>
                                </div>

                                <div class="details-section">
                                    <h3>Hábitos Diários</h3>
                                    <p><strong>Funcionamento Intestinal:</strong> ${data.intestino}</p>
                                    <p><strong>Alimentação:</strong> ${data.alimentacao}</p>
                                    <p><strong>Uso de Anticoncepcional:</strong> ${data.anticoncepcional}</p>
                                    <p><strong>Gestante:</strong> ${data.gestante}</p>
                                    ${data.gestante === 'sim' ? `<p><strong>Meses de Gestação:</strong> ${data.meses}</p>` : ''}
                                </div>

                                <div class="details-section">
                                    <h3>Preferências Pessoais</h3>
                                    <p><strong>Não gosta de massagem em alguma região?:</strong> ${data.nao_gosta}</p>
                                    ${data.nao_gosta === 'sim' ? `<p><strong>Regiões:</strong> ${data.nao_gosta_detalhes}</p>` : ''}
                                </div>

                                <div class="details-section">
                                    <h3>Histórico Clínico</h3>
                                    <p><strong>Estresse:</strong> ${data.estresse}</p>
                                    <p><strong>Distúrbio Renal:</strong> ${data.disturbio_renal}</p>
                                    ${data.disturbio_renal === 'sim' ? `<p><strong>Qual distúrbio renal:</strong> ${data.disturbio_renal_desc}</p>` : ''}
                                    <p><strong>Enxaqueca:</strong> ${data.enxaqueca}</p>
                                    <p><strong>Antecedentes Oncológicos:</strong> ${data.antecedentes_oncologicos}</p>
                                    ${data.antecedentes_oncologicos === 'sim' ? `<p><strong>Quais antecedentes:</strong> ${data.antecedentes_oncologicos_desc}</p>` : ''}
                                    <p><strong>Informações Relevantes de Saúde:</strong> ${data.saude_relevante || 'Não relatado'}</p>
                                    <p><strong>Depressão:</strong> ${data.depressao}</p>
                                    <p><strong>Insônia:</strong> ${data.insonia}</p>
                                    <p><strong>Pedras nos Rins:</strong> ${data.pedras_rins}</p>
                                    <p><strong>Pedras na Vesícula:</strong> ${data.pedras_vesicula}</p>
                                    <p><strong>Dor na Mandíbula:</strong> ${data.dor_mandibula}</p>
                                    <p><strong>Bruxismo:</strong> ${data.bruxismo}</p>
                                    <p><strong>Lentes de Contato:</strong> ${data.lentes_contato}</p>
                                    <p><strong>Possui alguma doença:</strong> ${data.doenca}</p>
                                    ${data.doenca === 'sim' ? `<p><strong>Qual doença:</strong> ${data.doenca_desc}</p>` : ''}
                                </div>

                                <div class="details-section">
                                    <h3>Informações do Sistema</h3>
                                    <p><strong>Status:</strong> ${data.status}</p>
                                    <p><strong>Data de Criação:</strong> ${formatDateTime(data.data_criacao)}</p>
                                </div>
                            </div>
                        `;

                        $('#modal-content').html(modalContent);
                        $('#anamnese-modal').show();
                    }
                }
            });
        });

        // Close modal
        $('.close').click(function() {
            $('#anamnese-modal').hide();
        });

        // Fechar modal ao clicar fora dele
        $(window).click(function(event) {
            if ($(event.target).is('#anamnese-modal')) {
                $('#anamnese-modal').hide();
            }
        });

        // Botão de excluir
        $('.delete-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var id = button.data('id');

            if (confirm('Tem certeza que deseja excluir este registro?')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_anamnese',
                        id: id,
                        nonce: anamneseEstetica.nonce
                    },
                    beforeSend: function() {
                        button.prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            button.closest('tr').fadeOut(400, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('Erro ao excluir o registro: ' + response.data.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao excluir o registro. Tente novamente.');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                    }
                });
            }
        });

        // Função auxiliar para formatar datas
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Função auxiliar para formatar data e hora
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Print handler
        $('.print-form').off('click').on('click', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_form_details',
                    id: id,
                    nonce: anamneseEstetica.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        const currentDate = new Date();
                        const dataFormatada = currentDate.toLocaleDateString('pt-BR', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                        const horaFormatada = currentDate.toLocaleTimeString('pt-BR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        const printContent = `
                            <div class="print-content">
                                <div class="print-header">
                                    <h1>Formulário de Anamnese</h1>
                                    <p class="print-date">Data: ${dataFormatada} - ${horaFormatada}</p>
                                </div>
                                
                                <div class="print-section">
                                    <h2>Dados Pessoais</h2>
                                    <p><strong>Nome:</strong> ${data.nome}</p>
                                    <p><strong>Data de Nascimento:</strong> ${formatDate(data.data_nascimento)}</p>
                                    <p><strong>Idade:</strong> ${data.idade} anos</p>
                                    <p><strong>Celular:</strong> ${data.celular}</p>
                                </div>

                                <div class="print-section">
                                    <h2>Objetivo</h2>
                                    <p><strong>Objetivo com a massagem:</strong> ${data.objetivo}</p>
                                    <p><strong>Dores:</strong> ${data.dor || 'Não relatado'}</p>
                                </div>

                                <div class="print-section">
                                    <h2>Histórico de Saúde</h2>
                                    <p><strong>Pressão Alta sem Medicação:</strong> ${data.pressao}</p>
                                    <p><strong>Diabetes não Controlada:</strong> ${data.diabetes}</p>
                                    <p><strong>Alergias:</strong> ${data.alergia || 'Não relatado'}</p>
                                    <p><strong>Sintomas nas Pernas:</strong> ${data.sintomas || 'Não relatado'}</p>
                                </div>

                                <div class="print-section">
                                    <h2>Hábitos Diários</h2>
                                    <p><strong>Funcionamento Intestinal:</strong> ${data.intestino}</p>
                                    <p><strong>Alimentação:</strong> ${data.alimentacao}</p>
                                    <p><strong>Uso de Anticoncepcional:</strong> ${data.anticoncepcional}</p>
                                    <p><strong>Gestante:</strong> ${data.gestante}</p>
                                    ${data.gestante === 'sim' ? `<p><strong>Meses de Gestação:</strong> ${data.meses}</p>` : ''}
                                </div>

                                <div class="print-section">
                                    <h2>Preferências Pessoais</h2>
                                    <p><strong>Não gosta de massagem em alguma região?:</strong> ${data.nao_gosta}</p>
                                    ${data.nao_gosta === 'sim' ? `<p><strong>Regiões:</strong> ${data.nao_gosta_detalhes}</p>` : ''}
                                </div>

                                <div class="print-section">
                                    <h2>Histórico Clínico</h2>
                                    <p><strong>Estresse:</strong> ${data.estresse}</p>
                                    <p><strong>Distúrbio Renal:</strong> ${data.disturbio_renal}</p>
                                    ${data.disturbio_renal === 'sim' ? `<p><strong>Qual distúrbio renal:</strong> ${data.disturbio_renal_desc}</p>` : ''}
                                    <p><strong>Enxaqueca:</strong> ${data.enxaqueca}</p>
                                    <p><strong>Antecedentes Oncológicos:</strong> ${data.antecedentes_oncologicos}</p>
                                    ${data.antecedentes_oncologicos === 'sim' ? `<p><strong>Quais antecedentes:</strong> ${data.antecedentes_oncologicos_desc}</p>` : ''}
                                    <p><strong>Informações Relevantes de Saúde:</strong> ${data.saude_relevante || 'Não relatado'}</p>
                                    <p><strong>Depressão:</strong> ${data.depressao}</p>
                                    <p><strong>Insônia:</strong> ${data.insonia}</p>
                                    <p><strong>Pedras nos Rins:</strong> ${data.pedras_rins}</p>
                                    <p><strong>Pedras na Vesícula:</strong> ${data.pedras_vesicula}</p>
                                    <p><strong>Dor na Mandíbula:</strong> ${data.dor_mandibula}</p>
                                    <p><strong>Bruxismo:</strong> ${data.bruxismo}</p>
                                    <p><strong>Lentes de Contato:</strong> ${data.lentes_contato}</p>
                                    <p><strong>Possui alguma doença:</strong> ${data.doenca}</p>
                                    ${data.doenca === 'sim' ? `<p><strong>Qual doença:</strong> ${data.doenca_desc}</p>` : ''}
                                </div>

                                <div class="print-section signatures">
                                    <h2>Termo de Responsabilidade</h2>
                                    <p class="disclaimer">Eu, ${data.nome}, declaro estar ciente sobre todos os benefícios e contraindicações relacionados à massoterapia.</p>
                                    
                                    <div class="signature-container">
                                        <div class="signature-box">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do Cliente</p>
                                            <p class="signature-date">${dataFormatada} - ${horaFormatada}</p>
                                        </div>

                                        <div class="signature-box">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do Profissional</p>
                                            <p class="signature-date">${dataFormatada} - ${horaFormatada}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(`
                            <html>
                                <head>
                                    <title>Imprimir Anamnese</title>
                                    <style>
                                        body {
                                            font-family: Arial, sans-serif;
                                            line-height: 1.6;
                                            padding: 20px;
                                            max-width: 800px;
                                            margin: 0 auto;
                                        }
                                        .print-header {
                                            text-align: center;
                                            margin-bottom: 30px;
                                            border-bottom: 2px solid #000;
                                            padding-bottom: 10px;
                                        }
                                        .print-header h1 {
                                            margin: 0;
                                            font-size: 24px;
                                        }
                                        .print-date {
                                            margin: 5px 0 0 0;
                                            font-size: 14px;
                                        }
                                        .print-section {
                                            margin-bottom: 20px;
                                            page-break-inside: avoid;
                                        }
                                        .print-section h2 {
                                            font-size: 18px;
                                            margin-bottom: 10px;
                                            border-bottom: 1px solid #ccc;
                                            padding-bottom: 5px;
                                        }
                                        .signatures {
                                            margin-top: 50px;
                                        }
                                        .signature-container {
                                            display: flex;
                                            justify-content: space-between;
                                            margin-top: 30px;
                                        }
                                        .signature-box {
                                            width: 45%;
                                            text-align: center;
                                        }
                                        .signature-line {
                                            border-bottom: 1px solid #000;
                                            margin-bottom: 5px;
                                            height: 40px;
                                        }
                                        .signature-box p {
                                            margin: 5px 0;
                                            font-size: 14px;
                                        }
                                        .signature-date {
                                            font-size: 12px !important;
                                            color: #666;
                                        }
                                        .disclaimer {
                                            margin-bottom: 20px;
                                            font-style: italic;
                                        }
                                        @media print {
                                            body {
                                                padding: 0;
                                            }
                                            .print-section {
                                                page-break-inside: avoid;
                                            }
                                        }
                                    </style>
                                </head>
                                <body>
                                    ${printContent}
                                    <script>
                                        window.onload = function() {
                                            window.print();
                                        }
                                    </script>
                                </body>
                            </html>
                        `);
                    }
                }
            });
        });
    }
});
