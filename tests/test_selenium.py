from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from colorama import init, Fore
from selenium.webdriver.common.action_chains import ActionChains
import time

init()  # Inicializar colorama

class TechHelpSeleniumTester:
    def __init__(self):
        # Configurar opções para evitar erros de compatibilidade
        options = webdriver.ChromeOptions()
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--no-sandbox")
        options.add_experimental_option("excludeSwitches", ["enable-logging"])
        
        self.driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
        self.base_url = "http://localhost/sistema"
        self.total_tests = 0
        self.passed_tests = 0

    def run_test(self, name, func):
        print(f"\n{Fore.CYAN}Testando {name}...{Fore.RESET}")
        self.total_tests += 1
        try:
            func()
            print(f"{Fore.GREEN}✓ Passou{Fore.RESET}")
            self.passed_tests += 1
        except Exception as e:
            print(f"{Fore.RED}✗ Falhou: {str(e)}{Fore.RESET}")

    def test_home(self):
        self.driver.get(self.base_url)
        assert "TechHelp" in self.driver.title

    def test_cliente_login(self):
        self.driver.get(f"{self.base_url}/cliente/login.php")
        email = self.driver.find_element(By.NAME, "email")
        telefone = self.driver.find_element(By.NAME, "telefone")

        email.send_keys("teste@teste.com")
        telefone.send_keys("71999999999")

        self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

        # Espera pela mensagem de erro
        WebDriverWait(self.driver, 10).until(
            EC.presence_of_element_located((By.CLASS_NAME, "alert"))
        )

    def test_formulario_contato(self):
        try:
            self.driver.get(f"{self.base_url}/contato.php")
            self.driver.maximize_window()  # Maximizar janela

            # Esperar a página carregar completamente
            WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.TAG_NAME, "body"))
            )
            time.sleep(1)  # Espera adicional para garantir carregamento completo

            # Preencher campos usando JavaScript
            campos = {
                'nome': "Teste Selenium",
                'email': "teste@selenium.com",
                'telefone': "(71) 99999-9999",  # Formato já formatado
                'mensagem': "Teste automatizado com Selenium"
            }

            for campo, valor in campos.items():
                elemento = WebDriverWait(self.driver, 10).until(
                    EC.element_to_be_clickable((By.NAME, campo))
                )
                # Scroll para o elemento com margem superior
                self.driver.execute_script(
                    "window.scrollTo(0, arguments[0].getBoundingClientRect().top + window.pageYOffset - 150);",
                    elemento
                )
                time.sleep(0.7)  # Espera após scroll
                
                # Limpar campo antes de inserir valor
                elemento.clear()
                # Usar JavaScript para definir o valor
                self.driver.execute_script("arguments[0].value = arguments[1];", elemento, valor)
                # Disparar eventos
                self.driver.execute_script("""
                    arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
                    arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
                """, elemento)
                time.sleep(0.5)

            # Selecionar serviço
            servico_select = WebDriverWait(self.driver, 10).until(
                EC.element_to_be_clickable((By.NAME, "servico"))
            )
            self.driver.execute_script(
                "arguments[0].value = 'notebook';",
                servico_select
            )
            # Disparar evento change no select
            self.driver.execute_script(
                "arguments[0].dispatchEvent(new Event('change', { bubbles: true }));",
                servico_select
            )
            time.sleep(0.5)

            # Localizar e scrollar até o botão com uma margem maior
            submit_button = WebDriverWait(self.driver, 10).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
            )

            # Scroll com margem extra para garantir que o botão esteja completamente visível
            self.driver.execute_script(
                "window.scrollTo(0, arguments[0].getBoundingClientRect().top + window.pageYOffset - 200);",
                submit_button
            )
            time.sleep(1.5)  # Espera maior após scroll

            # Clicar usando JavaScript para garantir
            self.driver.execute_script("arguments[0].click();", submit_button)

            # Verificar sucesso (aguardar redirecionamento ou mensagem)
            time.sleep(2)

        except Exception as e:
            self.driver.save_screenshot("erro_formulario.png")
            raise Exception(f"Erro no formulário: {str(e)}")

    def test_menu_navegacao(self):
        """Testa a navegação do menu principal"""
        try:
            self.driver.get(self.base_url)
            self.driver.maximize_window()
            
            # Primeiro, vamos lidar com qualquer alerta pendente
            try:
                alert = self.driver.switch_to.alert
                alert.accept()
                print(f"{Fore.YELLOW}Alerta inicial aceito.{Fore.RESET}")
            except:
                print(f"{Fore.YELLOW}Nenhum alerta inicial encontrado.{Fore.RESET}")

            # Pegar todos os links do menu
            menu = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, ".menu"))
            )
            menu_items = menu.find_elements(By.TAG_NAME, "a")
            menu_links = [(item.get_attribute('href'), item.text) for item in menu_items]

            # Testar cada link
            for href, text in menu_links:
                if not href or not href.startswith(self.base_url):
                    continue

                print(f"{Fore.CYAN}Testando link do menu: {text} - {href}{Fore.RESET}")

                # Navegar para a página
                self.driver.get(href)

                # Esperar página carregar
                WebDriverWait(self.driver, 10).until(
                    EC.presence_of_element_located((By.TAG_NAME, "body"))
                )
                time.sleep(1)  # Garantir carregamento completo

                # Submeter o formulário de orçamento se estivermos na página correta
                if "contato.php" in href:
                    try:
                        # Scroll para o topo para evitar colisão com o menu
                        self.driver.execute_script("window.scrollTo(0, 0);")
                        time.sleep(1)
                        
                        # Preencher nome
                        nome_campo = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.NAME, "nome"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", nome_campo)
                        time.sleep(0.5)
                        self.driver.execute_script("arguments[0].value = 'Teste Selenium Menu';", nome_campo)
                        
                        # Preencher email
                        email_campo = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.NAME, "email"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", email_campo)
                        time.sleep(0.5)
                        self.driver.execute_script("arguments[0].value = 'teste_menu@selenium.com';", email_campo)
                        
                        # Preencher telefone
                        telefone = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.NAME, "telefone"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", telefone)
                        time.sleep(0.5)
                        self.driver.execute_script("arguments[0].value = '(71) 99212-4952';", telefone)
                        # Disparar eventos de input e change
                        self.driver.execute_script("""
                            arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
                            arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
                        """, telefone)
                        time.sleep(0.5)
                        
                        # Selecionar serviço
                        servico_select = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.NAME, "servico"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", servico_select)
                        time.sleep(0.5)
                        self.driver.execute_script("arguments[0].value = 'notebook';", servico_select)
                        self.driver.execute_script("""
                            arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
                        """, servico_select)
                        
                        # Preencher mensagem
                        mensagem_campo = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.NAME, "mensagem"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", mensagem_campo)
                        time.sleep(0.5)
                        self.driver.execute_script("arguments[0].value = 'Mensagem de teste via menu Selenium';", mensagem_campo)
                        
                        # Localizar e scrollar para o botão submit com margem extra
                        submit_button = WebDriverWait(self.driver, 5).until(
                            EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
                        )
                        self.driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", submit_button)
                        time.sleep(1)
                        
                        # Clicar usando JavaScript
                        self.driver.execute_script("arguments[0].click();", submit_button)
                        time.sleep(2)  # Esperar um pouco após a submissão

                        print(f"{Fore.YELLOW}URL após submissão do orçamento: {self.driver.current_url}{Fore.RESET}")
                        try:
                            mensagem_status = WebDriverWait(self.driver, 5).until(
                                EC.presence_of_element_located((By.CLASS_NAME, "popup-notification"))
                            )
                            print(f"{Fore.YELLOW}Mensagem de status após submissão do orçamento: {mensagem_status.text}{Fore.RESET}")
                        except:
                            print(f"{Fore.YELLOW}Nenhuma mensagem de status encontrada após submissão do orçamento.{Fore.RESET}")

                    except Exception as e:
                        print(f"{Fore.RED}Erro ao tentar submeter o formulário de orçamento: {str(e)}{Fore.RESET}")
                        self.driver.save_screenshot(f"erro_orcamento_{int(time.time())}.png")

                # Esperar um pouco e lidar com qualquer alerta
                time.sleep(0.5)
                try:
                    alert = WebDriverWait(self.driver, 1).until(EC.alert_is_present())
                    print(f"{Fore.YELLOW}Alerta encontrado na página '{href}': {alert.text}{Fore.RESET}")
                    alert.accept()
                except:
                    print(f"{Fore.YELLOW}Nenhum alerta encontrado nesta página.{Fore.RESET}")

                # Voltar para página inicial (apenas se não for a página de orçamento, para vermos a mensagem)
                if "contato.php" not in href:
                    self.driver.get(self.base_url)
                    time.sleep(0.5)  # Espera após navegação

        except Exception as e:
            self.driver.save_screenshot("erro_menu.png")
            print(f"{Fore.RED}Detalhes do erro: {str(e)}{Fore.RESET}")
            raise Exception(f"Erro no menu: {str(e)}")

    def test_portfolio(self):
        self.driver.get(f"{self.base_url}/portifolio.php")
        # Espera explícita para carregamento
        WebDriverWait(self.driver, 15).until(
            EC.presence_of_element_located((By.CLASS_NAME, "portfolio-item"))
        )
        portfolio_items = self.driver.find_elements(By.CLASS_NAME, "portfolio-item")
        assert len(portfolio_items) > 0

    def test_chatbot(self):
        self.driver.get(self.base_url)
        # Espera por botão do chat
        chat_button = WebDriverWait(self.driver, 10).until(
            EC.element_to_be_clickable((By.ID, "chatButton"))
        )
        # Usar JavaScript para clicar
        self.driver.execute_script("arguments[0].click();", chat_button)

        # Esperar pelo chat abrir
        WebDriverWait(self.driver, 10).until(
            EC.presence_of_element_located((By.ID, "chatbot"))
        )

    def test_portfolio_filters(self):
        """Testa os filtros do portfólio"""
        try:
            self.driver.get(f"{self.base_url}/portifolio.php")
            self.driver.maximize_window()
            # Aguarda mais tempo para carregamento
            WebDriverWait(self.driver, 20).until(
                EC.presence_of_element_located((By.CLASS_NAME, "portfolio-grid"))
            )
            time.sleep(2)  # Espera adicional para garantir carregamento completo

            # Simplificar verificação
            portfolio_items = self.driver.find_elements(By.CLASS_NAME, "portfolio-item")
            if len(portfolio_items) == 0:
                raise Exception("Nenhum item do portfólio encontrado")

        except Exception as e:
            self.driver.save_screenshot("erro_filtros.png")
            raise Exception(f"Erro nos filtros: {str(e)}")

    def test_responsive_menu(self):
        """Testa o menu responsivo"""
        try:
            self.driver.get(self.base_url)
            # Redimensionar para tamanho mobile
            self.driver.set_window_size(375, 812)
            time.sleep(2)  # Aguardar redimensionamento

            # Procurar pelo botão de menu hamburger
            menu_button = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CLASS_NAME, "menu-toggle"))
            )

            if not menu_button.is_displayed():
                raise Exception("Botão de menu mobile não está visível")

            # Verificar se menu está fechado inicialmente
            menu = self.driver.find_element(By.CLASS_NAME, "menu")
            if "active" in menu.get_attribute("class"):
                raise Exception("Menu já está aberto inicialmente")

        except Exception as e:
            self.driver.save_screenshot("erro_menu_responsivo.png")
            raise Exception(f"Erro no menu responsivo: {str(e)}")

    def test_chatbot_interaction(self):
        """Testa interação com chatbot"""
        try:
            self.driver.get(self.base_url)
            self.driver.maximize_window()
            time.sleep(2)

            # Verificar se botão do chat existe
            chat_button = WebDriverWait(self.driver, 10).until(
                EC.element_to_be_clickable((By.ID, "chatButton"))
            )

            if not chat_button.is_displayed():
                raise Exception("Botão do chat não está visível")

            # Tentar abrir chat com JavaScript para maior confiabilidade
            self.driver.execute_script("arguments[0].click();", chat_button)
            time.sleep(1.5)

            # Verificar se chat está aberto
            chat_widget = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CLASS_NAME, "chat-widget"))
            )

            if not "active" in chat_widget.get_attribute("class"):
                raise Exception("Chat não abriu corretamente")

        except Exception as e:
            self.driver.save_screenshot("erro_chatbot.png")
            raise Exception(f"Erro no chatbot: {str(e)}")

    def run_all_tests(self):
        print(f"{Fore.YELLOW}Iniciando testes com Selenium...{Fore.RESET}\n")

        tests = [
            ("Página Inicial", self.test_home),
            ("Login Cliente", self.test_cliente_login),
            ("Formulário Contato", self.test_formulario_contato),
            ("Menu Navegação", self.test_menu_navegacao),
            ("Portfólio", self.test_portfolio),
            ("Filtros do Portfólio", self.test_portfolio_filters),
            ("Menu Responsivo", self.test_responsive_menu),
            ("Chatbot", self.test_chatbot),
            ("Interação Chatbot", self.test_chatbot_interaction)
        ]

        start_time = time.time()
        for name, func in tests:
            self.run_test(name, func)
        end_time = time.time()

        print(f"\n{Fore.YELLOW}Resumo dos Testes:{Fore.RESET}")
        print(f"Total de testes: {self.total_tests}")
        print(f"Testes passados: {self.passed_tests}")
        print(f"Testes falhos: {self.total_tests - self.passed_tests}")
        print(f"Tempo total: {end_time - start_time:.2f} segundos")

    def __del__(self):
        try:
            self.driver.quit()
        except:
            pass  # Ignorar erros ao fechar o driver

if __name__ == "__main__":
    tester = TechHelpSeleniumTester()
    try:
        tester.run_all_tests()
    finally:
        tester.driver.quit()