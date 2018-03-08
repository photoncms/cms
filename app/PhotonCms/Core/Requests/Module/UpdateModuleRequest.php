<?php

namespace Photon\PhotonCms\Core\Requests\Module;

use Photon\PhotonCms\Core\Requests\Request;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;

class UpdateModuleRequest extends Request
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
    public function rules(FieldTypeRepository $fieldTypeRepository, FieldTypeGateway $fieldTypeGateway)
    {
        $rules = [
            'module'            => 'array',
            'module.name'       => 'string|max:255',
            'module.anchor_text'=> 'nullable|string|max:2000',
            'module.anchor_html'=> 'nullable|string',
            'module.slug'       => 'nullable|string',
            'module.category'   => 'integer',
            'module.icon'       => 'string|max:255',
            'module.lazy_loading' => 'boolean',
            'fields'            => 'required|array'
        ];

        if($this->request->has('fields')) {
            foreach ($this->request->get('fields') as $key => $val) {
                $rules['fields.' . $key . '.id'] = 'integer'; // If not sent, field is treated as new
                if (!isset($val['id'])) {
                    // These fields are not mandatory if we are just trying to update a field
                    $rules['fields.' . $key . '.type'] = 'required|int';
                    $rules['fields.' . $key . '.name'] = 'required|string|max:255';

                    $fieldType = $fieldTypeRepository->findById($val['type'], $fieldTypeGateway);

                    if ($fieldType->isRelation()) {
                        $rules['fields.' . $key . '.related_module'] = 'required|int';
                        $rules['fields.' . $key . '.relation_name'] = 'required|string|max:255';
                    }
                    elseif (isset($val['virtual']) && ($val['virtual'] == 1 || $val['virtual'] === true)) {
                        $rules['fields.' . $key . '.virtual_name'] = 'required|string|max:255';
                    }
                    else {
                        $rules['fields.' . $key . '.column_name'] = 'required|string|max:255';
                    }
                }

                $rules['fields.' . $key . '.tooltip_text'] = 'nullable|string|max:255';
                $rules['fields.' . $key . '.validation_rules'] = 'nullable|string|max:255';
                $rules['fields.' . $key . '.editable'] = 'boolean';
                $rules['fields.' . $key . '.disabled'] = 'boolean';
                $rules['fields.' . $key . '.hidden'] = 'boolean';
                $rules['fields.' . $key . '.is_system'] = 'boolean';
                $rules['fields.' . $key . '.lazy_loading'] = 'boolean';
            }
        }

        return $rules;
    }
}
