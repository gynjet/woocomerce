<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do banco de dados
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do banco de dados - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'sql_chipless_gyn' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'sql_chipless_gyn' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 'GYNjet@2010' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'JSZ)16M@H:^aOgt,xS4`xCZUsO.)bOPMv2!R/{LHkR7;m>U{~N[T68r{4a9gEcc|' );
define( 'SECURE_AUTH_KEY',  ':@m_jE)#`|F@c4elk>UizC(Ad.[EtS{:,Z|dmaGI`F%yJo,*J4BX1Zzw#),.0l$i' );
define( 'LOGGED_IN_KEY',    'N&LcuWc8U7-)+/,2YK2O4~nJ~1}C2x~)KW7kB,iP=B{?9#)O.Vm(!T8x,~D/m0q2' );
define( 'NONCE_KEY',        'Q^(*J7qrkSm1.jEZ{.KJhc 5XvM h<qi)t5~K6Wc?pbGiFyES_3}aYc@/X>Fobp[' );
define( 'AUTH_SALT',        'h-5![x~y|h0>dY!MLBGn-[=aeiL>Nx2M@y|bR,-jun(X8Kz3wF$MMP|Kmnrv7j:{' );
define( 'SECURE_AUTH_SALT', ',TIn/QOGA|<Irus2BiN*y:_NhsymW)o&QqQk5FD<JEYNW>X;`fN~_h*O5ySn|`rZ' );
define( 'LOGGED_IN_SALT',   ',?tAhVk|!i~OVZu1#Ze}<Rty(o6_gXm}9W0DG,#2y]&%C`-o]au+~]l1l7#A ,W|' );
define( 'NONCE_SALT',       '&6{H :<NiwB.wMg!19w>(G+ZtTY9HC?-M_2U_X%hS%s7vkT|&G]>q+^w|)22ER_q' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Adicione valores personalizados entre esta linha até "Isto é tudo". */

// >>> Aqui é o local correto para as linhas de memória:
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';
