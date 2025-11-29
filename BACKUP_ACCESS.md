# Proteção de arquivos de backup sensíveis

Existem cópias de `wp-config.php` na raiz (`wp-config.php.backup`, `wp-config.bak-...`). Para evitar exposição pública:

1. **Bloqueio via servidor web** (recomendado):
   - Apache: criar/ajustar um `.htaccess` na raiz **(em staging primeiro)** contendo:
     ```apache
     <FilesMatch "^wp-config.*\.bak|^wp-config\.php\.backup$">
       Require all denied
     </FilesMatch>
     ```
   - Nginx: adicionar regra equivalente em `nginx.conf`/`sites-available` negando acesso a `wp-config*.bak`.
2. **Permissões de arquivo**: garantir que os backups não sejam world-readable (`chmod 640 wp-config*.bak*`) e propriedade do usuário de deploy.
3. **Rotina de limpeza**: mover backups antigos para um local fora do docroot ou para storage seguro/versionado após validação.
4. **Monitoramento**: manter scanner de arquivos públicos (Ex.: WP-CLI `wp vuln check` ou varredura de arquivos expostos) e logs do servidor para acessos 404 a backups.

> Observação: não removemos nem alteramos os backups existentes em produção sem aprovação; as ações acima podem ser aplicadas com janela de manutenção ou primeiro em staging.
