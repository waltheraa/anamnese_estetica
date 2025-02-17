<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="anamnese-form-container">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="anamnese-success-message">
            Formulário enviado com sucesso!
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="anamnese-form">
        <?php wp_nonce_field('anamnese_form_action', 'anamnese_form_nonce'); ?>

        <!-- Dados Pessoais -->
        <div class="form-section">
            <h3>Dados Pessoais</h3>
            <div class="form-group">
                <label for="nome" class="required-field">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="data-nascimento" class="required-field">Data de nascimento:</label>
                <input type="date" id="data-nascimento" name="data-nascimento" required>
            </div>

            <div class="form-group">
                <label for="idade" class="required-field">Idade:</label>
                <input type="number" id="idade" name="idade" readonly required>
            </div>

            <div class="form-group">
                <label for="celular" class="required-field">Celular:</label>
                <input type="tel" id="celular" name="celular" required>
            </div>
        </div>

        <!-- Objetivo -->
        <div class="form-section">
            <h3>Objetivo</h3>
            <div class="form-group">
                <label for="objetivo" class="required-field">Qual o seu maior objetivo com a massagem?</label>
                <textarea id="objetivo" name="objetivo" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="dor" class="required-field">Tem alguma dor neste momento? Onde?</label>
                <textarea id="dor" name="dor" rows="2" required></textarea>
            </div>
        </div>

        <!-- Histórico de Saúde -->
        <div class="form-section">
            <h3>Histórico de Saúde</h3>
            
            <div class="form-group radio-group">
                <label class="required-field">Tem pressão alta e não toma remédio?</label>
                <div class="radio-options">
                    <label><input type="radio" name="pressao" value="nao" required> Não</label>
                    <label><input type="radio" name="pressao" value="sim"> Sim</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Tem diabetes e não está controlada?</label>
                <div class="radio-options">
                    <label><input type="radio" name="diabetes" value="nao" required> Não</label>
                    <label><input type="radio" name="diabetes" value="sim"> Sim</label>
                </div>
            </div>

            <div class="form-group">
                <label for="alergia" class="required-field">Tem alergia ou aversão a algum creme, óleo ou aroma? Se sim, relate:</label>
                <textarea id="alergia" name="alergia" rows="2" required></textarea>
            </div>

            <div class="form-group">
                <label for="sintomas" class="required-field">Sente suas pernas quentes, formigando, vermelhas ou com dor? Se sim, relate:</label>
                <textarea id="sintomas" name="sintomas" rows="2" required></textarea>
            </div>
        </div>

        <!-- Hábitos Diários -->
        <div class="form-section">
            <h3>Hábitos Diários</h3>
            
            <div class="form-group radio-group">
                <label class="required-field">Funcionamento intestinal:</label>
                <div class="radio-options">
                    <label><input type="radio" name="intestino" value="regular" required> Regular</label>
                    <label><input type="radio" name="intestino" value="irregular"> Irregular</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Alimentação:</label>
                <div class="radio-options">
                    <label><input type="radio" name="alimentacao" value="boa" required> Boa</label>
                    <label><input type="radio" name="alimentacao" value="regular"> Regular</label>
                    <label><input type="radio" name="alimentacao" value="péssima"> Péssima</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Uso de anticoncepcional:</label>
                <div class="radio-options">
                    <label><input type="radio" name="anticoncepcional" value="sim" required> Sim</label>
                    <label><input type="radio" name="anticoncepcional" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Gestante:</label>
                <div class="radio-options">
                    <label><input type="radio" name="gestante" value="sim" id="gestante-sim" required> Sim</label>
                    <label><input type="radio" name="gestante" value="nao" id="gestante-nao"> Não</label>
                </div>
            </div>

            <div id="meses-container" class="form-group" style="display: none;">
                <label for="meses">Se sim, quantos meses?</label>
                <input type="number" id="meses" name="meses" min="1" max="9">
            </div>
        </div>

        <!-- Histórico Clínico -->
        <div class="form-section">
            <h3>Histórico Clínico</h3>
            
            <div class="form-group radio-group">
                <label class="required-field">Estresse:</label>
                <div class="radio-options">
                    <label><input type="radio" name="estresse" value="sim" required> Sim</label>
                    <label><input type="radio" name="estresse" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Distúrbio renal:</label>
                <div class="radio-options">
                    <label><input type="radio" name="disturbio-renal" value="sim" required> Sim</label>
                    <label><input type="radio" name="disturbio-renal" value="nao"> Não</label>
                </div>
            </div>
            <div class="form-group conditional-field" id="disturbio-renal-desc-container">
                <label for="disturbio-renal-desc">Se sim, qual?</label>
                <textarea id="disturbio-renal-desc" name="disturbio-renal-desc" rows="2"></textarea>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Enxaqueca:</label>
                <div class="radio-options">
                    <label><input type="radio" name="enxaqueca" value="sim" required> Sim</label>
                    <label><input type="radio" name="enxaqueca" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Antecedentes Oncológicos:</label>
                <div class="radio-options">
                    <label><input type="radio" name="antecedentes-oncologicos" value="sim" required> Sim</label>
                    <label><input type="radio" name="antecedentes-oncologicos" value="nao"> Não</label>
                </div>
            </div>
            <div class="form-group conditional-field" id="antecedentes-oncologicos-desc-container">
                <label for="antecedentes-oncologicos-desc">Se sim, qual?</label>
                <textarea id="antecedentes-oncologicos-desc" name="antecedentes-oncologicos-desc" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label for="saude-relevante" class="required-field">Há algo relevante em sua saúde para relatar?</label>
                <textarea id="saude-relevante" name="saude-relevante" rows="2" required></textarea>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Depressão:</label>
                <div class="radio-options">
                    <label><input type="radio" name="depressao" value="sim" required> Sim</label>
                    <label><input type="radio" name="depressao" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Insônia:</label>
                <div class="radio-options">
                    <label><input type="radio" name="insonia" value="sim" required> Sim</label>
                    <label><input type="radio" name="insonia" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Tem pedras nos rins?</label>
                <div class="radio-options">
                    <label><input type="radio" name="pedras-rins" value="sim" required> Sim</label>
                    <label><input type="radio" name="pedras-rins" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Tem pedras na vesícula?</label>
                <div class="radio-options">
                    <label><input type="radio" name="pedras-vesicula" value="sim" required> Sim</label>
                    <label><input type="radio" name="pedras-vesicula" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Dor na mandíbula:</label>
                <div class="radio-options">
                    <label><input type="radio" name="dor-mandibula" value="sim" required> Sim</label>
                    <label><input type="radio" name="dor-mandibula" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Bruxismo:</label>
                <div class="radio-options">
                    <label><input type="radio" name="bruxismo" value="sim" required> Sim</label>
                    <label><input type="radio" name="bruxismo" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Lentes de contato ocular:</label>
                <div class="radio-options">
                    <label><input type="radio" name="lentes-contato" value="sim" required> Sim</label>
                    <label><input type="radio" name="lentes-contato" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group radio-group">
                <label class="required-field">Possui alguma doença?</label>
                <div class="radio-options">
                    <label><input type="radio" name="doenca" value="sim" required> Sim</label>
                    <label><input type="radio" name="doenca" value="nao"> Não</label>
                </div>
            </div>
            <div class="form-group conditional-field" id="doenca-desc-container">
                <label for="doenca-desc">Se sim, qual?</label>
                <textarea id="doenca-desc" name="doenca-desc" rows="2"></textarea>
            </div>
        </div>

        <!-- Preferências Pessoais -->
        <div class="form-section">
            <h3>Preferências Pessoais</h3>
            
            <div class="form-group radio-group">
                <label class="required-field">Tem alguma parte do corpo onde NÃO gosta de receber massagem?</label>
                <div class="radio-options">
                    <label><input type="radio" name="nao_gosta" value="sim" required> Sim</label>
                    <label><input type="radio" name="nao_gosta" value="nao"> Não</label>
                </div>
            </div>

            <div class="form-group conditional-field" id="nao_gosta_detalhes_container">
                <label for="nao_gosta_detalhes">Se sim, informe qual região:</label>
                <textarea id="nao_gosta_detalhes" name="nao_gosta_detalhes" rows="2"></textarea>
            </div>
        </div>

        <!-- Termo de Responsabilidade -->
        <div class="form-section">
            <h3>Termo de Responsabilidade</h3>
            <p>Eu, declaro estar ciente sobre todos os benefícios e contraindicações relacionados à massoterapia. As declarações acima são verdadeiras, não cabendo ao profissional a responsabilidade por informações omitidas.</p>
            
            <div class="form-group">
                <label for="assinatura-cliente" class="required-field">Assinatura do cliente:</label>
                <input type="text" id="assinatura-cliente" name="assinatura-cliente" required>
            </div>

            <div class="form-group">
                <label for="assinatura-profissional">Assinatura do profissional:</label>
                <input type="text" id="assinatura-profissional" name="assinatura-profissional" value="Joide de Oliveira Soares Alves" readonly>
            </div>
        </div>

        <div class="form-submit">
            <input type="submit" name="anamnese_submit" value="Enviar" class="submit-button">
        </div>
    </form>
</div>
