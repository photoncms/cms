<?php

namespace Photon\PhotonCms\Core\Requests\Menu;

use Photon\PhotonCms\Core\Requests\Request;

class UpdateMenuItemRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'menu_link_type_id'     => 'exists:menu_link_types,id',
            'title'                 => 'string|max:255',
            'resource_data'         => 'string',
            'entry_data'            => 'string',
            'icon'                  => 'string|max:255',
            'slug'                  => 'string|max:255|unique:menu_items,slug',
            'parent_id'             => 'exists:menu_items,id'
        ];
        
        return $rules;
    }
}
