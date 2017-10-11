<?php

/*
Plugin Name: Duplicate Menu
Plugin URI: https://github.com/jchristopher/duplicate-menu
Description: Easily duplicate your WordPress Menus
Author: Jonathan Christopher
Version: 0.2.1
Author URI: http://mondaybynoon.com
*/

/*  Copyright 2011-2017 Jonathan Christopher (email : jonathan@mondaybynoon.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'DUPLICATE_MENU_VERSION',   '0.2.1' );
define( 'DUPLICATE_MENU_DIR',       plugin_dir_path( __FILE__ ) );
define( 'DUPLICATE_MENU_URL',       plugin_dir_url( __FILE__ ) );

function duplicate_menu_options_page() {
    add_theme_page( 'Duplicate Menu', 'Duplicate Menu', 'edit_theme_options', 'duplicate-menu', array( 'DuplicateMenu', 'options_screen' ) );
}

add_action( 'admin_menu', 'duplicate_menu_options_page' );

/**
 * Duplicate Menu
 */
class DuplicateMenu {

    /**
     * The duplication process
     */
    function duplicate( $id = null, $name = null ) {

        // sanity check
        if ( empty( $id ) || empty( $name ) ) {
	        return false;
        }

        $id = intval( $id );
        $name = sanitize_text_field( $name );
        $source = wp_get_nav_menu_object( $id );
        $source_items = wp_get_nav_menu_items( $id );
        $new_id = wp_create_nav_menu( $name );

        if ( ! $new_id ) {
            return false;
        }

        // key is the original db ID, val is the new
        $rel = array();

        $i = 1;
        foreach ( $source_items as $menu_item ) {
            $args = array(
                'menu-item-db-id'       => $menu_item->db_id,
                'menu-item-object-id'   => $menu_item->object_id,
                'menu-item-object'      => $menu_item->object,
                'menu-item-position'    => $i,
                'menu-item-type'        => $menu_item->type,
                'menu-item-title'       => $menu_item->title,
                'menu-item-url'         => $menu_item->url,
                'menu-item-description' => $menu_item->description,
                'menu-item-attr-title'  => $menu_item->attr_title,
                'menu-item-target'      => $menu_item->target,
                'menu-item-classes'     => implode( ' ', $menu_item->classes ),
                'menu-item-xfn'         => $menu_item->xfn,
                'menu-item-status'      => $menu_item->post_status
            );

            $parent_id = wp_update_nav_menu_item( $new_id, 0, $args );

            $rel[$menu_item->db_id] = $parent_id;

            // did it have a parent? if so, we need to update with the NEW ID
            if ( $menu_item->menu_item_parent ) {
                $args['menu-item-parent-id'] = $rel[$menu_item->menu_item_parent];
                $parent_id = wp_update_nav_menu_item( $new_id, $parent_id, $args );
            }

	        // allow developers to run any custom functionality they'd like
	        do_action( 'duplicate_menu_item', $menu_item, $args );

            $i++;
        }

        return $new_id;
    }

    /*
     * Output the options screen
     */
    function options_screen() {
        $nav_menus = wp_get_nav_menus();
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
            <h2><?php _e( 'Duplicate Menu' ); ?></h2>

            <?php if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['duplicate_menu_nonce'], 'duplicate_menu' ) ) : ?>
                <?php
                    $source         = intval( $_POST['source'] );
                    $destination    = sanitize_text_field( $_POST['new_menu_name'] );

                    // go ahead and duplicate our menu
                    $duplicator = new DuplicateMenu();
                    $new_menu_id = $duplicator->duplicate( $source, $destination );
                ?>

                <div id="message" class="updated"><p>
                    <?php if ( $new_menu_id ) : ?>
                        <?php _e( 'Menu Duplicated' ) ?>. <a href="nav-menus.php?action=edit&amp;menu=<?php echo absint( $new_menu_id ); ?>"><?php _e( 'View' ) ?></a>
                    <?php else: ?>
                        <?php _e( 'There was a problem duplicating your menu. No action was taken.' ) ?>.
                    <?php endif; ?>
                </p></div>

            <?php endif; ?>


            <?php if ( empty( $nav_menus ) ) : ?>
                <p><?php _e( "You haven't created any Menus yet." ); ?></p>
            <?php else: ?>
                <form method="post" action="">
                    <?php wp_nonce_field( 'duplicate_menu','duplicate_menu_nonce' ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="source"><?php _e( 'Duplicate this menu' ); ?>:</label>
                            </th>
                            <td>
                                <select name="source">
                                    <?php foreach ( (array) $nav_menus as $_nav_menu ) : ?>
                                        <option value="<?php echo esc_attr($_nav_menu->term_id) ?>">
                                            <?php echo esc_html( $_nav_menu->name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span style="display:inline-block; padding:0 10px;"><?php _e( 'and call it' ); ?></span>
                                <input name="new_menu_name" type="text" id="new_menu_name" value="" class="regular-text" />
                            </td>
                    </table>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button-primary" value="Duplicate Menu" />
                    </p>
                </form>
            <?php endif; ?>
        </div>
    <?php }
}
