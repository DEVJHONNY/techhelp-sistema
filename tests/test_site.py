import requests
import mysql.connector
from colorama import init, Fore
import time

init()  # Inicializar colorama

class TechHelpTester:
    def __init__(self):
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
    
    def test_database(self):
        try:
            conn = mysql.connector.connect(
                host="localhost",
                port=3306,
                user="root",
                password="",
                database="techhelp"
            )
            cursor = conn.cursor()
            cursor.execute("SHOW TABLES")
            tables = cursor.fetchall()
            required_tables = ['clientes', 'orcamentos', 'portfolio', 'servicos']
            
            for table in required_tables:
                if not any(table in t for t in tables):
                    raise Exception(f"Tabela {table} não encontrada")
                    
            conn.close()
        except Exception as e:
            raise Exception(f"Erro no banco de dados: {str(e)}")
    
    def test_pages(self):
        pages = ['', 'sobre.php', 'servicos.php', 'portifolio.php', 'contato.php']
        for page in pages:
            response = requests.get(f"{self.base_url}/{page}")
            if response.status_code != 200:
                raise Exception(f"Página {page} retornou status {response.status_code}")
    
    def test_cliente_login(self):
        data = {
            'email': 'teste@teste.com',
            'telefone': '71999999999'
        }
        response = requests.post(f"{self.base_url}/cliente/login.php", data=data)
        if response.status_code != 200:
            raise Exception("Login do cliente falhou")
    
    def test_security_headers(self):
        response = requests.get(self.base_url)
        required_headers = [
            'X-Frame-Options',
            'X-XSS-Protection',
            'X-Content-Type-Options'
        ]
        for header in required_headers:
            if header not in response.headers:
                raise Exception(f"Header de segurança {header} não encontrado")
    
    def test_assets(self):
        assets = [
            'assets/css/style.css',
            'assets/js/main.js',
            'assets/css/cliente.css'
        ]
        for asset in assets:
            response = requests.get(f"{self.base_url}/{asset}")
            if response.status_code != 200:
                raise Exception(f"Asset {asset} não encontrado")

    def test_forms(self):
        # Testar formulário de contato
        contact_data = {
            'nome': 'Teste',
            'email': 'teste@teste.com',
            'telefone': '71999999999',
            'servico': 'notebook',
            'mensagem': 'Teste automatizado'
        }
        response = requests.post(f"{self.base_url}/processa_contato.php", data=contact_data)
        if response.status_code != 200:
            raise Exception("Formulário de contato falhou")
    
    def test_api_endpoints(self):
        # Testar API de tracking
        response = requests.get(f"{self.base_url}/api/tracking.php?orcamento=1")
        if response.status_code != 200:
            raise Exception("API de tracking falhou")

    def test_database_structure(self):
        conn = mysql.connector.connect(
            host="localhost",
            port=3306,
            user="root",
            password="",
            database="techhelp"
        )
        cursor = conn.cursor()
        
        # Verifica estrutura das tabelas
        required_columns = {
            'orcamentos': ['id', 'id_cliente', 'servico_solicitado', 'status'],
            'clientes': ['id', 'nome', 'email', 'telefone'],
            'portfolio': ['id', 'titulo', 'descricao', 'imagem']
        }
        
        for table, columns in required_columns.items():
            cursor.execute(f"SHOW COLUMNS FROM {table}")
            table_columns = [column[0] for column in cursor.fetchall()]
            for required_column in columns:
                if required_column not in table_columns:
                    raise Exception(f"Coluna {required_column} não encontrada na tabela {table}")
        
        conn.close()

    def run_all_tests(self):
        print(f"{Fore.YELLOW}Iniciando testes do TechHelp...{Fore.RESET}\n")
        
        tests = [
            ("Conexão com Banco de Dados", self.test_database),
            ("Páginas Principais", self.test_pages),
            ("Login de Cliente", self.test_cliente_login),
            ("Headers de Segurança", self.test_security_headers),
            ("Assets do Site", self.test_assets),
            ("Formulários", self.test_forms),
            ("APIs", self.test_api_endpoints),
            ("Estrutura do Banco", self.test_database_structure)
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

if __name__ == "__main__":
    tester = TechHelpTester()
    tester.run_all_tests()
