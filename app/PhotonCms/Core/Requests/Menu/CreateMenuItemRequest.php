<?php

namespace Photon\PhotonCms\Core\Requests\Menu;

use Photon\PhotonCms\Core\Requests\Request;

class CreateMenuItemRequest extends Request
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
            'menu_id'               => 'required|exists:menus,id',
            'menu_link_type_id'     => 'required|exists:menu_link_types,id',
            'title'                 => 'required|string|max:255',
            'resource_data'         => 'required_if:menu_link_type_id,1,4|string',
            'entry_data'            => 'required_if:menu_link_type_id,4|string',
            'icon'                  => 'string|max:255',
            'slug'                  => 'string|max:255|unique:menu_items,slug',
            'parent_id'             => 'int|exists:menu_items,id'
        ];
        
        return $rules;
    }

    public function messages()
    {
        $messages = [
            "resource_data.required_if" => "The resource data field is required.",
            "entry_data.required_if" => "The entry data field is required."
        ];

        return $messages;
    }
}
