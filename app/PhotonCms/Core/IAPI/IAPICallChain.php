<?php

namespace Photon\PhotonCms\Core\IAPI;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\IAPI\Decoders\IAPIDecoderFactory;
use Photon\PhotonCms\Core\IAPI\Contracts\IAPICallChainStepInterface;

class IAPICallChain
{
    /**
     * Name of the mocked HTTP request method.
     * This value is automatically set.
     *
     * @var string
     */
    private $method;

    /**
     * Predefined allowed mocked HTTP methods.
     * Method names are used as a final function call in the call chain. You must end a call chain with one of these methods to activate the chain decoder.
     *
     * @var array
     */
    private $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * An array of call chain steps.
     * Composed of an array of IAPIAttributeStep and IAPIFunctionStep.
     * This is automatically filled, do not change it manually.
     *
     * @var array
     */
    private $steps;

    /**
     * Call chain mocked HTTP method parameters.
     * Every call chain must end with a mocked HTTP method, like $IAPI->something1->something2()->post([parameters]);
     * Parameters sent with this method are going to be stored into this variable.
     *
     * @var array
     */
    private $methodParameters;

    /**
     * An array of GET method call chain step definitions.
     * This represents routes.
     *
     * @var array
     */
    private $GET = [
        // NODES
        [// Get node ancestors Example: $IAPI->nodes->albums->ancestors(1)->get();
            'type' => 'attribute',
            'name' => 'nodes',
            'steps' => [
                [
                    'type' => 'attribute',
                    'name' => '*',
                    'steps' => [
                        [
                            'type' => 'function',
                            'argumentCount' => 1,
                            'name' => 'ancestors',
                            'decoder' => 'GetNodeAncestorsDecoder'
                        ]
                    ]
                ]
            ]
        ],
        [// Get node children Example: $IAPI->nodes->albums(1)->get(['child_modules' => ['albums', 'lyrics']]);
            'type' => 'attribute',
            'name' => 'nodes',
            'steps' => [
                [
                    'type' => 'function',
                    'argumentCount' => 1,
                    'name' => '*',
                    'decoder' => 'GetNodeChildrenDecoder'
                ]
            ]
        ],
        [// Get root nodes Example: $IAPI->nodes->albums->get();
            'type' => 'attribute',
            'name' => 'nodes',
            'steps' => [
                [
                    'type' => 'attribute',
                    'name' => '*',
                    'decoder' => 'GetRootNodesDecoder'
                ]
            ]
        ],
        // MODULES
        [// Module entry extension call Example: $IAPI->extension_call->users(1)->test->get('test1', 'test2');
            'type' => 'attribute',
            'name' => 'extension_call',
            'steps' => [
                [
                    'type' => 'function',
                    'argumentCount' => 1,
                    'name' => '*',
                    'steps' => [
                        [
                            'type' => 'attribute',
                            'name' => '*',
                            'decoder' => 'ModuleEntryExtensionCallDecoder'
                        ]
                    ]
                ]
            ]
        ],
        [// Get module item Example: $IAPI->users(1)->get();
            'type' => 'function',
            'argumentCount' => 1,
            'name' => '*',
            'decoder' => 'GetModuleItemDecoder'
        ],
        [// Get all module items Example: $IAPI->users->get();
            'type' => 'attribute',
            'name' => '*',
            'decoder' => 'GetModuleEntriesDecoder'
        ]
    ];

    /**
     * An array of POST method call chain step definitions.
     * This represents routes.
     *
     * @var array
     */
    private $POST = [
        // MODULES
        [// Filter module entries Example: $IAPI->filter->news->post(['filter' => ['author' => ['equal' => 1]], 'pagination' => ['items_per_page' => 2, 'current_page' => 1], 'sorting' => ['id' => 'desc']]);
            'type' => 'attribute',
            'name' => 'filter',
            'steps' => [
                [
                    'type' => 'attribute',
                    'name' => '*',
                    'decoder' => 'FilterModuleEntriesDecoder'
                ]
            ]
        ],
        [// Count module entries Example: $IIAPI->count->users->post(['filter' => ['confirmed' => ['equal' => 1]]]);
            'type' => 'attribute',
            'name' => 'count',
            'steps' => [
                [
                    'type' => 'attribute',
                    'name' => '*',
                    'decoder' => 'CountModuleEntriesDecoder'
                ]
            ]
        ],
        [// Create module entry Example: $IAPI->news->post(['title' => 'Some title', 'content' => 'Some content', 'author' => 1]);
            'type' => 'attribute',
            'name' => '*',
            'decoder' => 'CreateModuleEntryDecoder'
        ]
    ];

    /**
     * An array of PUT method call chain step definitions.
     * This represents routes.
     *
     * @var array
     */
    private $PUT = [
        // NODES
        [// Update module entry Example: $IAPI->nodes->reposition->put(['action' => 'setScope', 'affected' => ['table' => 'products', 'id' => 1], 'target' => ['id' => 1]]);
            'type' => 'attribute',
            'name' => 'nodes',
            'steps' => [
                [
                    'type' => 'attribute',
                    'name' => 'reposition',
                    'decoder' => 'RepositionNodeDecoder'
                ]
            ]
        ],
        // MODULES
        [// Update module entry Example: $IAPI->news(1)->put(['title' => 'true', 'content' => 'something']);
            'type' => 'function',
            'argumentCount' => 1,
            'name' => '*',
            'decoder' => 'UpdateModuleEntryDecoder'
        ],
        [// Update multiple module entries Example: $IAPI->news->put(['title' => 'true', 'filter' => ['user' => 1]]);
            'type' => 'attribute',
            'name' => '*',
            'decoder' => 'MassUpdateModuleEntryDecoder'
        ]
    ];

    /**
     * An array of DELETE method call chain step definitions.
     * This represents routes.
     *
     * @var array
     */
    private $DELETE = [
        // MODULES
        [// Delete module entry Example: $IAPI->news(1)->delete(['force' => true]);
            'type' => 'function',
            'argumentCount' => 1,
            'name' => '*',
            'decoder' => 'DeleteModuleEntryDecoder'
        ]
    ];

    /**
     * Manage a call chain attribute step.
     * This happens when an attribute is requested in the chain.
     *
     * @param string $name
     * @return \Photon\PhotonCms\Core\IAPI\IAPICallChain
     */
    public function __get($name)
    {
        $this->addStep(IAPIStepFactory::make($name));
        return $this;
    }

    /**
     * Manage a call chain function step.
     * This happens when function is clled in the chain.
     * If the function name matches to one of the $this->allowedMethods then the call chain gets resolved.
     *
     * @param string $name
     * @param array $arguments
     * @return \Photon\PhotonCms\Core\IAPI\IAPICallChain
     */
    public function __call($name, $arguments)
    {
        if (in_array(strtoupper($name), $this->allowedMethods)) {
            $this->method = $name;
            $this->methodParameters = $arguments;
            return $this->resolve();
        }

        $this->addStep(IAPIStepFactory::make($name, $arguments));
        return $this;
    }

    /**
     * Adds a new step to the call chain stack.
     *
     * @param IAPICallChainStepInterface $step
     */
    private function addStep(IAPICallChainStepInterface $step)
    {
        $this->steps[] = $step;
    }

    /**
     * Resolves a call chain and if the route is valid, the appropriate decoder is called.
     *
     * @return mixed
     * @throws PhotonException
     */
    private function resolve()
    {
        $decoderName = $this->stepInto($this->{strtoupper($this->method)});
        if ($decoderName) {
            $decoder = IAPIDecoderFactory::make($decoderName, $this->method);
            return $decoder->decode($this->steps, $this->methodParameters);
        }
        else {
            throw new PhotonException('UNDEFINED_IAPI_ROUTE');
        }
    }

    /**
     * Steps into a route level and tries to match a step to that level.
     *
     * @param array $stepMaps
     * @param int $level
     * @return null|Contracts\IAPIDecoderInterface
     */
    private function stepInto($stepMaps, $level = 0)
    {
        if (!key_exists($level, $this->steps)) {
            return null;
        }
        $step = $this->steps[$level];
        foreach ($stepMaps as $stepMap) {
            if ($this->matchStep($stepMap, $step)) {
                if (key_exists('decoder', $stepMap)) {
//                    var_dump('match found');
                    return $stepMap['decoder'];
                }
                elseif (key_exists('steps', $stepMap) && is_array($stepMap['steps']) && !empty ($stepMap['steps'])) {
                    $nestedSteps = $this->stepInto($stepMap['steps'], $level+1);
                    if ($nestedSteps) {
                        return $nestedSteps;
                    }
                }
                else {
                    continue;
                }
            }
            else {
                continue;
            }
        }
        return null;
    }

    /**
     * Checks if the specific step map from a route matches the step instance.
     *
     * @param string $stepMap
     * @param IAPICallChainStepInterface $step
     * @return boolean
     */
    private function matchStep($stepMap, $step)
    {
        if (
            $stepMap['type'] === $step->getType() &&
            (
                $stepMap['name'] === '*' ||
                $stepMap['name'] === $step->getName()
            ) &&
            (
                $stepMap['type'] === 'attribute' ||
                $stepMap['type'] === 'function' && $stepMap['argumentCount'] === $step->countArguments()
            )
        ) {
            return true;
        }
        return false;
    }
}