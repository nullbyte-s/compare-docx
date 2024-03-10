# Comparador de Documentos em PHP

Este é um sistema web simples em PHP que permite o upload de arquivos no formato .docx e comparação entre eles para identificar semelhanças.

## Funcionalidades

- Comparação de documentos para identificar semelhanças
- Exibição de arquivos semelhantes em uma tabela

### Exemplo
| Documento enviado | Documento comparado | Percentual de Semelhança |
| ----------------- | --------------------|------------------------- |
| Recente.docx      | Antigo.docx         | 72,4%                    |

## Pré-requisitos

- PHP 8.2 ou superior
- Composer para instalar as dependências do projeto

## Instalação

1. Clone o repositório para o seu ambiente local:

```
git clone https://github.com/nullbyte-s/compare-docx.git
```

2. Navegue até o diretório do projeto:

```
cd compare-docx
```

3. Instale as dependências do Composer:

```
composer install
```

4. Configure as permissões adequadas para todo o diretório:

```
chmod -R --exclude=index.php 755 /
```

## Uso

1. Após a instalação, você pode acessar o sistema navegando até o diretório raiz do projeto em seu servidor web. Certifique-se de rodá-lo em um servidor PHP.
2. Na página inicial, você verá um formulário para enviar arquivos .docx.
3. Envie um arquivo .docx para iniciar o processo de comparação.
4. A tabela será exibida e mostrará os arquivos semelhantes, se houver.
5. Você pode clicar no botão "Excluir" para remover um arquivo se a semelhança estiver entre 70% e 90%.
6. Acima de 90% é considerado o mesmo documento, com o que substituirá o documento antigo pelo novo.

## Contribuindo

Contribuições são bem-vindas! Se você encontrar problemas ou tiver sugestões de melhorias, fique à vontade para abrir uma issue ou enviar um pull request.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).