# Novas Funcionalidades - Quimbanda-JP v1.2.3

## 1. Controle de Opacidade do Background

### O que é?

Permite ajustar a transparência da imagem de fundo usando um controle deslizante (slider) no Customizer do WordPress.

### Como usar?

1. No painel administrativo, vá para **Aparência → Personalizar**
2. Clique em **Quimbanda-JP: Background**
3. Use o controle **"Opacidade da Imagem de Fundo (%)"**
4. Arraste o slider de **0 (transparente)** a **100 (opaco)**
5. Pressione **Publicar** para salvar

### Valores

- **100%** = Imagem completamente visível
- **75%** = Imagem com 75% de opacidade
- **50%** = Imagem com 50% de opacidade
- **0%** = Imagem completamente invisível (mostra apenas cor de fundo)

### Comportamento

Quando você reduz a opacidade abaixo de 100%:

1. A imagem fica mais transparente
2. A cor de fundo padrão (preto #121212) aparece por trás
3. Melhora a legibilidade do texto sobreposto
4. O efeito funciona apenas com imagens PNG/GIF, não afeta vídeos MP4

### Exemplo de Uso

- Configurar opacidade em **70%** para uma imagem de background que é muito escura
- Deixar em **100%** para fotos claras que já têm boa legibilidade
- Usar **40-50%** para background com padrões/texturas que podem distrair

---

## 2. Correção do Link do WhatsApp

### Problema Anterior

O número do WhatsApp não estava gerando links válidos, impedindo que o botão abrisse uma conversa.

**Causa:** O código removia TODOS os caracteres não-dígitos, incluindo o `+` necessário para o código de país (DDI).

### Solução Implementada

A função `qjp_get_whatsapp_link()` agora:

✅ Aceita números em múltiplos formatos:
- `5511999999999` (sem +)
- `+5511999999999` (com +)
- `11999999999` (sem DDI, assume Brasil)

✅ Mantém os dígitos e o `+` (se presente)

✅ Formata corretamente para `https://wa.me/NÚMERO`

✅ Registra no log de debug para diagnosticar problemas

### Como Testar?

1. Vá para **Aparência → Personalizar**
2. Clique em **Quimbanda-JP: Contato**
3. Insira seu número no campo **"Número do WhatsApp (com DDI)"**
   - Exemplo: `5511999999999` ou `+5511999999999`
4. Clique em **Publicar**
5. Acesse a página inicial e clique no botão **WhatsApp**
6. Deve abrir uma conversa no WhatsApp (Web ou App)

### Formatos Aceitos

| Formato | Exemplo | Resultado |
|---------|---------|-----------|
| Com DDI | 5511999999999 | `wa.me/5511999999999` ✅ |
| Com + e DDI | +5511999999999 | `wa.me/+5511999999999` ✅ |
| Só número (assume Brasil) | 11999999999 | `wa.me/5511999999999` ✅ |
| Sem dígitos válidos | abc | Link vazio ❌ |

### Log de Debug

Se o link não funcionar, verifique em **Quimbanda-JP → Debug & Logs**:

```
[2026-03-26 15:45:30] [INFO] Link WhatsApp gerado
Data: {"number":"5511999999999"}
```

---

## 3. Sistema de Debug & Logs (Anteriormente Implementado)

### Acessar o Painel

1. No admin, vá para **Aparência**
2. Clique em **Debug & Logs** (apenas se `WP_DEBUG` estiver ativado)
3. Visualize eventos e erros do tema
4. Limpe o log quando necessário

### Ativar Debug

Adicione ao `wp-config.php`:

```php
define( 'WP_DEBUG', true );
```

---

## 🐛 Solução de Problemas

### WhatsApp continua não funcionando?

**Passo 1:** Ativar Debug
```php
// wp-config.php
define( 'WP_DEBUG', true );
```

**Passo 2:** Verificar o formato do número

- Remova qualquer caractere que não seja dígito ou `+`
- Use DDI (código do país): Brasil = `55`
- Não inclua parênteses, hífens ou espaços

**Passo 3:** Verificar no log

Acesse **Quimbanda-JP → Debug & Logs** e procure por:
```
Link WhatsApp gerado
```

**Passo 4:** Testar URL manualmente

Abra seu navegador e acesse:
```
https://wa.me/5511999999999
```

Se abrir a conversa no WhatsApp, o tema está funcionando.

### Background muito escuro?

Use o novo controle de **Opacidade**:

1. Vá para **Aparência → Personalizar**
2. **Quimbanda-JP: Background**
3. Reduza a opacidade para **50-70%**
4. Isso deixará a cor de fundo visível por trás da imagem

---

## 📋 Resumo das Alterações

| Item | Antes | Depois |
|------|-------|--------|
| Opacidade Background | Sem controle | Slider 0-100% |
| WhatsApp Link | Removia `+` (quebrado) | Mantém `+` e dígitos |
| Suporte DDI | Apenas `55` | `+55`, `55`, ou sem DDI |
| Debug | Logs básicos | Painel completo com histórico |

---

## 🔄 Atualizar para v1.2.3

1. Faça backup do seu site
2. Vá para **Aparência → Temas**
3. Clique em **Atualizações do Tema** (se houver)
4. Clique em **Atualizar com backup automático**
5. Teste o novo controle de opacidade
6. Verifique se o WhatsApp funciona

---

**Versão:** 1.2.3  
**Data:** 26 de março de 2026  
**Autor:** Andre Silva TsC (https://andretsc.dev)
