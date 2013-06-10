<?php
/**
 * One to Many
 *
 * @category    WordPress
 * @package     OneToMany
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

function _otm_get_parent_type($post_type)
{
    $type = get_post_type_object($post_type);

    if (empty($type->otm_parent)) {
        return false;
    }

    return is_array($type->otm_parent) ? $type->otm_parent : array($type->otm_parent);
}

function _otm_get_objects(array $post_types)
{
    return get_posts(array(
        'nopaging'  => true,
        'post_type' => $post_types,
    ));
}

function otm_register_meta_box($post_type)
{
    if (!post_type_exists($post_type)) {
        return;
    }

    $parent = _otm_get_parent_type($post_type);

    if (!$parent) {
        return;
    }

    add_meta_box(
        'otm-page-parent',
        __('Parent', OTM_DOMAIN),
        'otm_meta_box_cb',
        $post_type,
        'side',
        'low',
        array('types' => $parent)
    );
}

function otm_meta_box_cb($post, array $box)
{
    $objects = _otm_get_objects($box['args']['types']);

    $select = '<select name="post_parent" class="otm-dropdown widefat">';
    $select .= sprintf('<option>%s</option>', esc_html__('None', OTM_DOMAIN));
    $select .= walk_page_dropdown_tree($objects, 0, array(
        'selected'  => $post->post_parent,
    ));
    $select .= '</select>';

    echo apply_filters('otm_dropdown', $select, $post, $objects);
}
