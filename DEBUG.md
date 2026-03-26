# Quimbanda-JP: Guia de Debug e Resolução de Problemas

## 🔍 Sistema de Logging e Debug

O tema Quimbanda-JP inclui um sistema robusto de logging para diagnosticar e resolver erros, especialmente aqueles relacionados à REST API do WordPress.

### Ativar Debug

Para ativar o debug completo, adicione ao arquivo `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

### Arquivo de Log do Tema

Quando o debug está ativado, o tema registra automaticamente:

- Adição de itens ao menu (WhatsApp)
- Verificação de atualização semanais
- Detecção de plugins ativos
- Erros e avisos do tema

**Localização:** `/wp-content/qjp-theme-debug.log`

### Painel de Debug no Admin

Com o debug ativado, você terá acesso a:

1. **Quimbanda-JP → Debug & Logs** no painel administrativo
2. Visualização em tempo real do arquivo de log
3. Limpeza manual do log
4. Informações do sistema

---

## 🐛 Problemas Comuns e Soluções

### Erro: "A resposta não é um JSON válido" ao editar posts

**Causa:** Conflito entre o tema e plugins que modificam o menu ou geram output antes de cabeçalhos HTTP serem enviados.

**Solução:**

1. **Ativar Debug** para ver detalhes no log
2. **Verificar plugins ativos:**
   - WPForms
   - Yoast SEO
   - Plugins de cache
   - Plugins de segurança

3. **Método de diagnóstico:**
   - Ative `WP_DEBUG` e `WP_DEBUG_LOG` no `wp-config.php`
   - Abra o painel de Debug & Logs
   - Tente editar um post no editor de blocos
   - Verifique o log para mensagens de erro

### REST API retorna status 200 mas não é JSON válido

**Verificar:**

```bash
# Testar endpoint da REST API
curl -i https://seusite.com/wp-json/wp/v2/posts
```

Se receber "JSON inválido", pode haver:

- Output não intencional de HTML antes de `wp_json_encode()`
- Caracteres BOM (UTF-8 BOM) no início de `functions.php`
- Plugins gerando erros ou avisos

### Conflito com WPForms

O tema foi revisado para não incluir suporte a WPForms. Se você estava usando:

1. Remova o suporte manual do `functions.php`
2. Limpe o cache do navegador
3. Teste novamente a edição de posts

---

## 🔧 Funções de Debug Disponíveis

### `qjp_log( $message, $level = 'info', $data = null )`

Registra evento no arquivo de log.

```php
qjp_log('Evento importante', 'info', ['chave' => 'valor']);
qjp_log('Algo deu errado!', 'error', $_POST);
qjp_log('Aviso do tema', 'warning');
```

### `qjp_get_debug_log()`

Retorna as últimas 500 linhas do log.

```php
echo qjp_get_debug_log();
```

### `qjp_clear_debug_log()`

Limpa o arquivo de log (requer `manage_options`).

```php
qjp_clear_debug_log();
```

---

## 📋 Checklist de Diagnóstico

- [ ] Ativar `WP_DEBUG` no `wp-config.php`
- [ ] Acessar painel **Quimbanda-JP → Debug & Logs**
- [ ] Verificar arquivo de log para erros
- [ ] Listar plugins ativos e desativar suspeitos
- [ ] Testar REST API com `curl` ou DevTools
- [ ] Limpar cache do navegador e WordPress
- [ ] Desativar plugins um por um para identificar conflito
- [ ] Verificar versão do PHP (mínimo 7.4)
- [ ] Verificar versão do WordPress (mínimo 5.9)

---

## 🛡️ Melhorias de Segurança Implementadas

O tema inclui validações defensivas contra:

1. **Erros de menu:** Verificação de tipo de objeto antes de acessar propriedades
2. **Output não intencional:** Uso de `sprintf()` com `esc_url()` e `esc_html()`
3. **Conflitos de filtro:** Logging de todas as operações de filtro
4. **Erros JSON:** Validação de resposta antes de `json_decode()`

---

## 📞 Ainda tem dúvidas?

Verifique o arquivo de log em `/wp-content/qjp-theme-debug.log` para mensagens detalhadas.

Se o erro continuar, entre em contato com o desenvolvedor do tema:
**Andre Silva TsC** - https://andretsc.dev

---

**Última atualização:** 26 de março de 2026  
**Versão do Tema:** 1.2.2
