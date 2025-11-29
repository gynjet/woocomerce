# Commerce Hygiene Audit

Pequenas ações de baixo risco recomendadas para manter a loja saudável. Todas podem ser executadas primeiro em staging ou com janela de manutenção curta.

- **Revisar contas administrativas**: confirmar usuários com função de administrador e remover/desativar contas antigas; validar que 2FA está ativo onde disponível.
- **Sessões e cookies**: limpar sessões expiradas no banco (`wp_woocommerce_sessions`) e validar tempo de expiração do carrinho via painel do WooCommerce.
- **Cron e fila de emails**: verificar se o WP-Cron não está desabilitado e se filas de email/transientes não estão crescendo; caso esteja usando cron do sistema, validar logs de execução.
- **Backups e staging**: garantir que cópias de segurança e ambientes de staging não estejam acessíveis publicamente; aplicar regras de bloqueio na pasta raiz conforme `BACKUP_ACCESS.md`.
- **Saúde da base de dados**: executar rotina de otimização das tabelas de log/carrinho em horários de baixo tráfego e revisar tamanho das tabelas de transientes.
- **Plugins de observabilidade**: manter ativo um plugin de monitoramento de erros (ex.: Query Monitor em staging) para identificar regressões rapidamente.
- **Lista de checagem pós-deploy**: manter checklist curta (testar checkout, login, carrinho, cálculo de frete) a cada atualização de plugin/tema.
