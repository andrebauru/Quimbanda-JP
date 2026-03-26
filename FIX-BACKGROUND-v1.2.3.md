# 🐛 Solução: Background Image Não Aparecia - v1.2.3

## Problema Resolvido

❌ **Antes:** Imagem de background selecionada no Customizer não aparecia na página

✅ **Agora:** Background image aparece corretamente com controle de opacidade

## O que foi corrigido?

### CSS Z-Index Hierarchy

O problema era o uso de `opacity` direto no `body`, que afetava toda a página.

**Antes (quebrado):**
```css
body {
  background-image: url(...);
  opacity: 0.7;  /* ❌ Deixa TODA página transparente */
}
```

**Depois (correto):**
```css
body::before {
  content: '';
  background-image: url(...);
  opacity: 0.7;  /* ✅ Só afeta a imagem */
  z-index: -2;   /* Atrás de tudo */
}

body::after {
  content: '';
  background: rgba(18,18,18,0.3);  /* Overlay escuro */
  z-index: -1;  /* Entre background e conteúdo */
}
```

### Stack de Z-Index

```
z-index: auto (0+)  = Conteúdo da página (posts, menu, etc) ← VISÍVEL
z-index: -1         = Overlay de escurecimento
z-index: -2         = Background image
```

## Como Testar?

### Passo 1: Atualizar o Tema

1. Faça backup do site
2. Vá para **Aparência → Temas**
3. Se houver opção de atualização, clique em **Atualizar com backup**
4. Caso contrário, desative e reative o tema

### Passo 2: Adicionar Imagem de Background

1. **Aparência → Personalizar**
2. **Quimbanda-JP: Background**
3. Selecione **"Tipo de background"** → **"Imagem (PNG/GIF)"**
4. Clique em **"Imagem de fundo (PNG/GIF)"** e selecione uma imagem
5. Clique em **Publicar**

### Passo 3: Verificar Resultado

Na página inicial:

✅ Imagem deve aparecer ao fundo
✅ Conteúdo (posts) permanece legível
✅ Menu e botões funcionam normalmente
✅ Imagem não "queimada" na página

### Passo 4: Ajustar Opacidade (Opcional)

1. Em **Quimbanda-JP: Background**
2. Ajuste **"Opacidade da Imagem de Fundo (%)"**
   - **100%** = Imagem totalmente visível
   - **70%** = Imagem com 70% de opacidade
   - **50%** = Imagem bem transparente
3. Clique em **Publicar**

Quando reduz a opacidade, a cor de fundo preta apareça por trás.

## Debug no Log

Se o background ainda não aparecer, ative o debug:

1. Adicione ao `wp-config.php`:
```php
define( 'WP_DEBUG', true );
```

2. Vá para **Aparência → Debug & Logs**

3. Procure por mensagens como:

```
[2026-03-26 15:45:30] [INFO] CSS Customizer aplicado
Data: {"bg_type":"image","bg_image":"sim","opacity":100}

[2026-03-26 15:45:30] [INFO] Background image aplicado
Data: {"url":"https://seusite.com/wp-content/uploads/2026/03/bg.png","opacity":1}
```

Se não ver essas mensagens:
- Verifique se selecionou uma imagem no Customizer
- Clique em **Publicar** (não é Auto-save)
- Limpe cache do navegador (Ctrl+F5)
- Limpe cache de plugins WordPress (se tiver WP Rocket, etc)

## Arquivos Modificados

- ✅ `functions.php` - Corrigido CSS com z-index e pseudo-elementos
- ✅ `v1.2.3` - Versão liberada

## Resumo Técnico

| Aspecto | Detalhes |
|---------|----------|
| **Raiz do Problema** | `opacity` no `body` afetava conteúdo |
| **Solução** | Usar `body::before` e `body::after` com z-index |
| **Z-Index -2** | Background image (atrás) |
| **Z-Index -1** | Overlay de escurecimento |
| **Z-Index 0+** | Conteúdo normal |
| **Logging** | Adicionado para diagnóstico |

## Próximas Atualizações

Se encontrar outros problemas, o sistema de debug registrará em:

```
/wp-content/qjp-theme-debug.log
```

Acesse **Aparência → Debug & Logs** para visualizar.

---

**Versão:** 1.2.3 (CORRIGIDA)  
**Data:** 26 de março de 2026  
**Status:** ✅ Testado e pronto para produção
