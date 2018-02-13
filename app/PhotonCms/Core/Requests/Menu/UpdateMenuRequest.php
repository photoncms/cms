<?php

namespace Photon\PhotonCms\Core\Requests\Menu;

use Photon\PhotonCms\Core\Requests\Request;

class UpdateMenuRequest extends Request
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
            'title'             => 'string|max:255',
            'max_depth'         => 'integer',
            'min_root'          => 'integer',
            'description'       => 'string',
            'menu_link_types'   => 'array|exists:menu_link_types,id'
        ];
        
        return $rules;
    }

    /**
     * Format sent data before it gets to validatior
     *
     * @return array
     */
    protected function getValidatorInstance()
    {
        $data = $this->all();
        
        // explode array if needed
        if(isset($data['menu_link_types']) && !is_array($data['menu_link_types']) && $data['menu_link_types'] != "") {
            $data['menu_link_types'] = explode(",", $data['menu_link_types']);
        }

        $this->getInputSource()->replace($data);

        return parent::getValidatorInstance();
    }
}
