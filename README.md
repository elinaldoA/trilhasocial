# Trilha Social

Trilha Social é uma aplicação web desenvolvida com o framework Laravel, utilizando Tailwind CSS para estilização e Vite para o gerenciamento de assets. O projeto tem como objetivo [descrever brevemente o propósito do projeto, por exemplo, "facilitar a gestão de trilhas de aprendizagem em ambientes educacionais"].

## 🚀 Tecnologias Utilizadas

- [Laravel](https://laravel.com/) – Framework PHP para desenvolvimento web
- [Tailwind CSS](https://tailwindcss.com/) – Framework utilitário para estilização
- [Vite](https://vitejs.dev/) – Ferramenta de build para front-end
- [Composer](https://getcomposer.org/) – Gerenciador de dependências PHP
- [PHPUnit](https://phpunit.de/) – Framework de testes para PHP

## 🛠️ Instalação

1. Clone o repositório:

   ```bash
   git clone https://github.com/elinaldoA/trilhasocial.git
   cd trilhasocial

2. Instale as dependências PHP:

   composer install

3. Instale as dependências JavaScript:

   npm install

4. Copie o arquivo de ambiente e configure as variáveis necessárias:

   cp .env.example .env
   php artisan key:generate

5. Configure o banco de dados no arquivo .env e execute as migrações:

   php artisan migrate

6. Inicie o servidor de desenvolvimento:

   php artisan serve

7. Compile os assets front-end:

   npm run dev
   
8. Testes

   php artisan test

