
# Quimbanda-JP (Tema WordPress)

![Preview do tema](screenshot.png)

## Estrutura recomendada

- Assets/Fonts/FingerPaint.ttf
- functions.php
- header.php
- index.php
- footer.php
- style.css
- sitemap.xml (exemplo)

## Observações de padrão WordPress

- `header.php` e `footer.php` separados para reutilização.
- `functions.php` com:
  - `after_setup_theme`
  - `wp_enqueue_scripts`
  - `widgets_init`
  - `customize_register`
- Suporte a `title-tag`, `post-thumbnails`, `html5`, `custom-logo` e menu principal.
- Text domain: `quimbanda-jp`.

## Sitemap XML

WordPress já gera sitemap automático em:

- `/wp-sitemap.xml`

O arquivo `sitemap.xml` deste tema é apenas um modelo estático.

---

Desenvolvido por [Andre Silva TsC](https://andretsc.dev)

---

## Mudanças recentes (26/03/2026)

- Verificação completa dos arquivos PHP do tema (`php -l`): sem erros de sintaxe.
- Identificado o motivo do ZIP grande: ZIPs antigos estavam dentro da pasta do tema e eram incluídos no pacote novo.
- Removidos ZIPs antigos da raiz para evitar empacotamento recursivo.
- Adicionado o script `build-theme-zip.ps1` para gerar pacote limpo.

### Gerar ZIP correto do tema

No PowerShell, dentro da pasta do tema:

`./build-theme-zip.ps1 -Version 1.2.4`

O script já exclui automaticamente:

- pastas de desenvolvimento (`.git`, `.vscode`)
- arquivos `.zip` antigos
- arquivos `.md`
- pasta temporária de build

Assim o arquivo final fica pronto para **Aparência > Temas > Enviar tema** sem inchar tamanho.

---

## Mudanças recentes (26/03/2026 - compatibilidade WP/plugins)

- Adicionados templates de compatibilidade do WordPress sem alterar o visual base:
  - `single.php`
  - `page.php`
  - `archive.php`
  - `search.php`
  - `404.php`
  - `comments.php`
  - `sidebar.php`
- Adicionado registro de widget area (`sidebar-1`) para suporte a plugins/widgets.
- Adicionado carregamento do script `comment-reply` em páginas singulares com comentários encadeados.
- Adicionados suportes extras de tema para melhor compatibilidade:
  - `customize-selective-refresh-widgets`
  - `wp-block-styles`
  - `responsive-embeds`
  - `align-wide`
- Confirmado e ajustado o comportamento de background:
  - quando for **imagem**, o fundo aparece atrás do conteúdo (`qjp-has-bg-image`)
  - quando for **vídeo**, o fundo aparece atrás do conteúdo (`qjp-has-bg-video`)
  - ajuste aplicado para manter `body` transparente nesses casos, preservando o layout atual.
