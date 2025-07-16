#!/bin/bash
# Script para criar menu institucional com links funcionais

# VARIÁVEL DE USUÁRIO (ajuste se necessário)
USER=www

# Cria o menu (caso não exista)
sudo -u $USER -- wp menu create institucional --allow-root

# Adiciona as páginas já existentes ao menu. Ajuste os slugs caso necessário!
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=como-comprar --field=ID --allow-root) --allow-root
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=perfil-de-cores --field=ID --allow-root) --allow-root
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=perguntas-frequentes --field=ID --allow-root) --allow-root
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=politicas-e-privacidade --field=ID --allow-root) --allow-root
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=regulamento-das-promocoes --field=ID --allow-root) --allow-root
sudo -u $USER -- wp menu item add-post institucional $(wp post list --post_type=page --name=trocas-e-devolucoes --field=ID --allow-root) --allow-root

# Exibe os locations disponíveis para você escolher onde colocar o menu
sudo -u $USER -- wp menu location list --allow-root
echo "⚠️ Veja acima o nome do location do seu rodapé! Exemplo comum: footer-menu"

# Atribua o menu ao local desejado (ajuste o nome conforme o resultado acima)
# sudo -u $USER -- wp menu location assign institucional footer-menu --allow-root
