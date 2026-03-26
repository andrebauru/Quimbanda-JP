# Guia de Compatibilidade: Inserindo Plugins no Quimbanda-JP

## ✅ Checklist antes de instalar qualquer plugin

### 1. **Verificar Compatibilidade com REST API**

O tema utiliza endpoints da REST API do WordPress para comunicação via AJAX/Fetch. Plugins que modificam filtros `wp_*_menu_items` ou `rest_*_query_vars` podem causar conflitos.

**Plugins Conhecidos com Conflitos:**

- ❌ **WPForms** - Intercepta filtros de menu (removido do tema)
- ❌ **Plugins de "Mini Cart"** - Modificam menu
- ⚠️ **Yoast SEO** - Alguns filtros podem interferir (teste com cuidado)
- ⚠️ **Elementor** - Substitui templates (testado em modo compatível)
- ✅ **Akismet** - Compatível
- ✅ **Jetpack** - Compatível (sem Protect)
- ✅ **WooCommerce** - Compatível com este tema

### 2. **Ativar Debug Antes de Instalar**

Sempre ative o debug **antes** de instalar um novo plugin:

```php
// wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Depois:

1. Instale o plugin
2. Ative o plugin
3. Vá para **Quimbanda-JP → Debug & Logs**
4. Teste as funcionalidades do site

### 3. **Problemas Comuns ao Integrar Plugins**

#### **Problema: "JSON não é válido" ao editar posts**

**Passos de resolução:**

```bash
# 1. Desativar todos os plugins
# 2. Testar edição de posts
# 3. Ativar um plugin por vez
# 4. Testar depois de cada ativação
```

**Verificar no log** (/wp-content/qjp-theme-debug.log):

```
[2026-03-26 14:30:45] [WARNING] args não é objeto no filtro wp_nav_menu_items
[2026-03-26 14:30:46] [ERROR] Plugin XYZ retornou tipo inválido
```

#### **Problema: Tema fica lento com plugin ativo**

Verifique se o plugin:

- Faz várias queries ao banco
- Carrega JS/CSS desnecessários no frontend
- Injeta HTML no footer sem verificação

#### **Problema: Menu principal desaparece**

Causa comum: Plugin que retorna `NULL` ou tipo inválido no filtro `wp_nav_menu_items`.

**Solução no functions.php do tema:**

```php
// O filtro agora valida o tipo de $args
function qjp_add_whatsapp_to_menu($items, $args)
{
    if (!is_object($args)) {
        qjp_log('args inválido', 'warning');
        return $items;  // Retorna items intactos
    }
    // ... resto do código
}
```

---

## 🔌 Plugins Recomendados para Usar com Quimbanda-JP

### **SEO & Performance**

| Plugin | Compatível | Notas |
|--------|-----------|-------|
| RankMath SEO | ✅ | Excelente, sem conflitos conhecidos |
| All in One SEO | ✅ | Testado, compatível |
| WP Rocket | ✅ | Cache compatível com o tema |
| Shortpixel | ✅ | Otimização de imagens |
| Smush | ✅ | Compressão de imagens |

### **Segurança**

| Plugin | Compatível | Notas |
|--------|-----------|-------|
| Wordfence Security | ✅ | Compatível |
| iThemes Security | ✅ | Testado |
| Akismet Anti-Spam | ✅ | Proteção de comentários |
| Brute Force Protection | ✅ | Leve, compatível |

### **Funcionalidade**

| Plugin | Compatível | Notas |
|--------|-----------|-------|
| Contact Form 7 | ✅ | Use com cuidado (ver abaixo) |
| Gravity Forms | ✅ | Testado, recomendado |
| WooCommerce | ✅ | E-commerce completo |
| MonsterInsights | ✅ | Google Analytics integrado |

### **Evitar ou Usar com Cuidado**

| Plugin | Recomendação | Motivo |
|--------|-------------|--------|
| WPForms | ❌ Evitar | Conflita com filtro de menu |
| Divi Builder | ⚠️ Cuidado | Substitui templates do tema |
| Visual Composer | ⚠️ Cuidado | Page builder que pode conflitar |
| Beaver Builder | ✅ Ok | Se usar em páginas específicas |

---

## 🧪 Teste de Compatibilidade Automático

Ao instalar um plugin, o tema executa automaticamente:

1. **Detecção de plugins ativos** - Registra em `/qjp-theme-debug.log`
2. **Verificação de menu** - Valida estrutura do filtro
3. **Teste de REST API** - Confirma respostas JSON válidas

**Para ver resultados:**

```
Quimbanda-JP → Debug & Logs → Conteúdo do Log
```

---

## 🛠️ Desenvolvendo Plugin Compatível com Quimbanda-JP

Se você está criando um plugin para usar com este tema, siga estas regras:

### ✅ **FAÇA:**

```php
// ✅ Bom: Verificar tipo e não gerar output
add_filter('wp_nav_menu_items', function($items, $args) {
    if (!is_object($args)) {
        return $items;
    }
    
    // Usar sprintf com escape
    $new_item = sprintf(
        '<li><a href="%s">%s</a></li>',
        esc_url($url),
        esc_html($text)
    );
    
    return $items . $new_item;
}, 10, 2);
```

### ❌ **NÃO FAÇA:**

```php
// ❌ Ruim: Output direto
add_filter('wp_nav_menu_items', function($items, $args) {
    echo "Novo item"; // ← Quebra REST API!
    return $items;
});

// ❌ Ruim: Concatenação insegura
$items .= "<li><a href='$url'>$text</a></li>"; // ← XSS risk!

// ❌ Ruim: Sem validação de tipo
if ($args->theme_location === 'primary') { // ← Erro se não for objeto!
    // ...
}
```

---

## 📊 Monitoramento Contínuo

### Habilitar Logging em Todos os Filtros

Se você precisa diagnosticar um problema específico, edite `functions.php`:

```php
// Adicionar antes da função problemática
add_action('qjp_weekly_update_check', function() {
    qjp_log('Update check iniciado', 'info');
});
```

### Criar Relatório de Saúde

```bash
# Via terminal (SSH)
tail -f /home/usuario/public_html/wp-content/qjp-theme-debug.log
```

---

## 🆘 Diagnosticar Erro "JSON Inválido"

### Passo 1: Ativar Debug

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Passo 2: Testar REST API

```bash
# Terminal/PowerShell
curl -i https://seusite.com/wp-json/wp/v2/posts

# Se retornar "JSON inválido" antes do JSON real:
# O problema é um plugin gerando output
```

### Passo 3: Consultar Log do Tema

Acesse: **Quimbanda-JP → Debug & Logs**

Procure por:

```
[WARNING] args não é objeto
[ERROR] Erro ao processar filtro
```

### Passo 4: Desativar Plugins

1. Painel → Plugins
2. Desativar **todos**
3. Testar REST API novamente
4. Ativar um por um
5. Testar após cada ativação

---

## 📈 Melhores Práticas

### Ao Atualizar o Tema

- [ ] Fazer backup primeiro
- [ ] Desativar cache do navegador
- [ ] Testar com 2-3 plugins principais
- [ ] Verificar Debug & Logs

### Ao Instalar Plugin Novo

- [ ] Instalar e ativar
- [ ] Acessar painel admin
- [ ] Ir a Quimbanda-JP → Debug & Logs
- [ ] Conferir se há mensagens de erro
- [ ] Testar REST API com `curl`

### Padrão de Cores do Log

```
[INFO]    - Informação normal
[WARNING] - Algo inusitado (mas funcionando)
[ERROR]   - Erro que precisa atenção
```

---

**Versão:** 1.2.2  
**Última atualização:** 26 de março de 2026  
**Desenvolvedor:** Andre Silva TsC (https://andretsc.dev)
