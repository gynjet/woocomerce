# Diagnóstico inicial do repositório WooCommerce

## Problemas identificados

1. **Repositório sem remoto configurado** – não há origem Git definida, o que impede `git push` para o GitHub ou qualquer outro serviço.
2. **Árvore de arquivos corrompida/poluída** – o histórico contém dezenas de arquivos com nomes que parecem pedaços de comandos de shell e SQL (ex.: `" && \\> wp --allow-root --path=\"$WP\" eval "`, `,`, `--fields=term_id,name,slug,parent,count`). Isso dificulta a manutenção e pode quebrar scripts de deploy que assumem um layout padrão de WordPress.
3. **Credenciais sensíveis commitadas** – backups de `wp-config.php` estão versionados com usuário, senha e host do banco de dados em texto plano, expondo segredos que não deveriam estar no Git.
4. **Sem pipeline de deploy** – não há configuração de CI/CD (ex.: pasta `.github/` ou scripts de build/deploy), então o repositório por si só não executa build nem entrega automática.

## Evidências

- Falta de remoto configurado: `git remote -v` não retorna nenhuma origem.
- Arquivos estranhos rastreados: `git ls-tree -r HEAD` lista nomes de arquivos que parecem comandos, em vez de estrutura normal de projeto.
- Configurações sensíveis versionadas: backups de `wp-config.php` trazem credenciais de banco de dados diretamente no repositório.
- Ausência de CI/CD: não existe pasta `.github/` ou equivalente para pipelines de deploy.

## Recomendações iniciais

- Definir o remoto correto (ex.: `git remote add origin <url-do-repo>`) e validar autenticação.
- Limpar a árvore Git removendo os arquivos de lixo e reescrevendo o histórico para excluí-los, garantindo que só o código necessário fique versionado.
- Rotacionar imediatamente as credenciais expostas e mover segredos para variáveis de ambiente/secret manager; remover e reescrever commits que contenham esses arquivos de backup.
- Adicionar uma pipeline de CI/CD mínima (ex.: GitHub Actions) que valide o projeto e publique nos ambientes desejados.
