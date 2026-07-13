<?php
/**
 * Plugin Name: BC — Habilitar Application Passwords sobre HTTP
 * Description: Permite la autenticación por Application Passwords aunque el entorno
 *              sea "production" y el sitio se sirva sin SSL. Necesario para que el
 *              MCP de WordPress (docdyhr/mcp-wordpress) y otros clientes REST puedan
 *              autenticarse contra este contenedor interno (http://wordpress).
 *
 * Contexto: el sitio corre en contenedores Podman detrás de la CDN; el tráfico
 * interno entre el MCP y WordPress es HTTP dentro de la red wp_bc_default. WordPress
 * core deshabilita Application Passwords sin SSL salvo en entorno 'local'. Este
 * mu-plugin reactiva esa capacidad de forma controlada.
 *
 * Reversible: eliminar este archivo restaura el comportamiento por defecto.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_is_application_passwords_available', '__return_true' );
add_filter( 'wp_is_application_passwords_available_for_user', '__return_true' );
