<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'alizesdeepamvb' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'alizesdeepamvb' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'Hoz888dar' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'alizesdeepamvb.mysql.db' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '=lCM_V;a0JKAi6+g:*}XS^uEy)p )a.FEO{<_vme@!x(=W#P(fu4|YU}/HC{jh_?' );
define( 'SECURE_AUTH_KEY',  'qTJZfg$b}P+_9,)&G?..g#|!c.ns#<S^pkQ/;1aUeAC,Ges{;-b%9X/*yi9sB2w`' );
define( 'LOGGED_IN_KEY',    '?J)B?lxSZaMWO%^OIDkkPpFoojQ%xUGf~]^A5|KPdMxh@|SOYj~z<|?lD]PXcIWr' );
define( 'NONCE_KEY',        'z Z~P6{it6euJS0A5[s;k,`r8FSm{lkQ]$PN.(?IuwUv[Fv@xu0GcGtPT,Q>rK+z' );
define( 'AUTH_SALT',        '?.7&8S~gw(GGS}f[!zIC|bUwd]r0lQ-)Lc5}bZ),s)1%lzc]Xsj0H>;_&XB=u2>+' );
define( 'SECURE_AUTH_SALT', 'JoQ}rcAQ9$^l@/]ZNE3%i1;&;+tuRGvH;PCB&)Tz/1lIstIS~#rji}Y2gPx$BdqX' );
define( 'LOGGED_IN_SALT',   'NjIZTec7kSWT|o!yZ_tBEj)iV%c]#pxwJd)f)EwWo4Hl9~a J@:_x]]|Z-K2Ex/R' );
define( 'NONCE_SALT',       'hSI#=60jwl6BD>ekpE#259FtTYM$Zz%o%X5O On.p1@!8KWFH3{n{9$[<z@H [[X' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
