#!/bin/bash

# Caminhos
IMAGE_DIR="/www/wwwroot/imagens.gynjet.com.br/produtos/t120"
URL_BASE="https://imagens.gynjet.com.br/produtos/t120/"
PHP_BIN="/www/server/php/82/bin/php"
WP_BIN="/usr/local/bin/wp"
WP_PATH="/www/wwwroot/loja.gynjet.com.br/wordpress"

# Loop para percorrer as imagens e cadastrar os produtos
for IMAGE in $(find $IMAGE_DIR -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.webp" \)); do
  # Extrair nome da imagem e gerar o SKU
  IMAGE_NAME=$(basename "$IMAGE")
  SKU=$(echo "$IMAGE_NAME" | sed 's/\.[^.]*$//')

  # Verificar se o produto já existe no WooCommerce
  PRODUCT_ID=$($PHP_BIN $WP_BIN post list --post_type=product --meta_key=_sku --meta_value="$SKU" --format=ids --allow-root --path=$WP_PATH)

  # Se o produto não existir, criar um novo
  if [ -z "$PRODUCT_ID" ]; then
    # Criar o produto
    PRODUCT_ID=$($PHP_BIN $WP_BIN post create --post_type=product --post_title="Produto HP T120 - $SKU" --post_status=publish --allow-root --path=$WP_PATH)

    # Atribuir SKU
    $PHP_BIN $WP_BIN post meta update $PRODUCT_ID _sku "$SKU" --allow-root --path=$WP_PATH

    # Definir título SEO para Yoast
    YOAST_TITLE="Produto HP T120 - $SKU"
    YOAST_DESC="Produto HP T120 para impressoras, SKU $SKU, peças Gynjet. Suporte técnico especializado em Goiânia."
    $PHP_BIN $WP_BIN post meta update $PRODUCT_ID _yoast_wpseo_title "$YOAST_TITLE" --allow-root --path=$WP_PATH
    $PHP_BIN $WP_BIN post meta update $PRODUCT_ID _yoast_wpseo_metadesc "$YOAST_DESC" --allow-root --path=$WP_PATH
    $PHP_BIN $WP_BIN post meta update $PRODUCT_ID _yoast_wpseo_focuskw "HP T120 $SKU" --allow-root --path=$WP_PATH

    # Associar imagem
    IMAGE_URL="${URL_BASE}${IMAGE_NAME}"
    $PHP_BIN $WP_BIN post meta update $PRODUCT_ID _product_image_gallery "$IMAGE_URL" --allow-root --path=$WP_PATH

    echo "Produto $SKU criado com sucesso."
  else
    echo "Produto com SKU $SKU já existe. Pulando..."
  fi
done

# Atualizar índices do WooCommerce
$PHP_BIN $WP_BIN eval 'wc_update_product_lookup_tables();' --allow-root --path=$WP_PATH
$PHP_BIN $WP_BIN transient delete --all --allow-root --path=$WP_PATH
