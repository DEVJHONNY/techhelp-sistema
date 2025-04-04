<?php 
$pagina = 'contato';
$titulo = "Solicitar Orçamento";
$pagina_css = "contato";
require_once 'includes/header.php';
require_once 'includes/nav.php';

// Pegar serviço da URL se existir
$servico_selecionado = $_GET['servico'] ?? '';
?>

<section class="page-header">
    <div class="container">
        <h1>Solicitar Orçamento</h1>
        <p>Preencha o formulário abaixo para solicitar seu orçamento</p>
    </div>
</section>

<section class="contato-page">
    <div class="container">
        <div class="contato-grid">
            <div class="form-container">
                <form action="processa_contato.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nome</label>
                        <input type="text" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Telefone</label>
                        <input type="tel" name="telefone" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tools"></i> Serviço</label>
                        <select name="servico" required>
                            <option value="">Selecione o serviço</option>
                            <option value="notebook" <?php echo $servico_selecionado == 'notebook' ? 'selected' : ''; ?>>
                                Manutenção de Notebook
                            </option>
                            <option value="pc" <?php echo $servico_selecionado == 'pc' ? 'selected' : ''; ?>>
                                Manutenção de PC
                            </option>
                            <option value="rede" <?php echo $servico_selecionado == 'rede' ? 'selected' : ''; ?>>
                                Serviços de Rede
                            </option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label><i class="fas fa-comment"></i> Descrição do Problema</label>
                        <textarea name="mensagem" rows="5" required></textarea>
                    </div>

                    <div class="file-upload">
                        <i class="fas fa-upload"></i>
                        <p>Anexar fotos do equipamento (opcional)</p>
                        <input type="file" name="fotos[]" multiple accept="image/*">
                        <small>Máximo 5 arquivos, 2MB cada</small>
                    </div>

                    <button type="submit" class="btn-enviar">
                        <i class="fas fa-paper-plane"></i> Enviar Solicitação
                    </button>
                </form>
            </div>

            <div class="contato-info">
                <div class="contato-item">
                    <i class="fas fa-phone"></i>
                    <h3>WhatsApp</h3>
                    <p>(71) 99212-4952</p>
                    <a href="https://wa.me/5571992124952" class="btn-whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i> Chamar no WhatsApp
                    </a>
                </div>

                <div class="contato-item">
                    <i class="fas fa-clock"></i>
                    <h3>Horário de Atendimento</h3>
                    <p>Segunda a Sexta: 8h às 18h</p>
                    <p>Sábado: 8h às 12h</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
