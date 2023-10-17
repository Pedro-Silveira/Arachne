# ARACHNE: Sistema de Portaria Digital

![Tela real capturada em 16/10/2023.](https://i.imgur.com/HJOf553.png)

> Sistema desenvolvido para o Segundo Esquadrão do Primeiro Grupo de Comunicações e Controle da Força Aérea Brasileira, substituindo as fichas manuais de controle de acesso do efetivo por um método digital.

## ⚙️ Funcionamento

O sistema, inicialmente, solicita que o usuário entre no sistema utilizando o login e senha cadastrados por um administrador. Nele, é possível registrar a entrada e saída de visitantes, retirada e devolução de chaves, além da entrada e saída de viaturas. As opções serão diferentes para cada tipo de usuário:

### 🔩 Usuário

Para usuários do tipo "usuário", o sistema não autoriza acesso à pagina inicial. Seu cadastro serve apenas para registro de retirada e devolução de chaves.

### 🔩 Sentinela

Para usuários do tipo "sentinela", o sistema autoriza acesso à pagina inicial. Os usuários deste grupo possuem permissão de incluir, editar e remover registros de entrada e saída de efetivo, retirada e devolução de chaves e entrada e saída de viaturas. Além disso, os usuários são capazes de gerar um arquivo PDF contendo os registros de um serviço qualquer.

### 🔩 Sargento de Dia

Para usuários do tipo "sargento de dia", o sistema inclui as permissões do tipo "sentinela" e, ainda, acrescenta a possibilidade de encerrar um serviço, agrupando, portanto, todos os registros incluídos durante o período em que esteve no plantão 24 horas, através de um botão disponível no menu de navegação da página inicial.

### 🔩 Administrador

Para usuários do tipo "administrador", o sistema inclui as permissão do tipo "sargento de dia" e, ainda, acrescenta a possibilidade de incluir, editar e remover registros de todos os serviços armazenados no banco de dados, tudo feito através de um painel de gerenciamento que pode ser acessado pelo menu de navegação da página inicial. Além disso, os administradores do sistema têm permissão de incluir, editar e remover registros de usuários, chaves e viaturas do sistema.

### ⌨️ Banco de Dados

O sistema possui três tabelas principais: _"servicos"_, a qual agrupa os registros feitos durante um plantão 24 horas; _"efetivo"_, a qual armazena as informações dos usuários; _"chaves"_, a qual mantém cadastradas as chaves disponíveis; e _"viaturas"_, a qual mantém cadastradas os veículos disponíveis.
Além disso, existem outras três tabelas secundárias: _"svvisitantes"_, a qual contém os registros de entrada e saída de visitantes; _"svchaves"_, a qual contém os registros de retirada e devolução de chaves; e _"svviaturas"_, a qual contém os registros de entrada e saída de veículos.

## 🛠️ Ferramentas

* [Visual Studio Code](https://code.visualstudio.com/) - IDE utilizada para escrita dos códigos em PHP.
* [phpMyAdmin](https://www.phpmyadmin.net/) - BD utilizado para armazenamento dos registros.
* [FPDF](http://www.fpdf.org/) - Classe para gerar arquivos PDF.

## 🎁 Menção Honrosa

Agradeço profundamente ao efetivo do 2°/1° GCC pelo incentivo, pelo apoio criativo, e pela confiança na implementação do sistema. A fim de evidenciar o processo de desenvolvimento, todos os registros textuais e fotográficos foram publicados na página oficial do órgão na Intranet.
