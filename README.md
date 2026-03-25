# Quimbanda-JP (Tema WordPress)

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
