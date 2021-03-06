<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SyncResponse.
 *
 * @method Model\Experiment[] getExperiments()
 * @method mixed getMessage()
 * @method bool getNoClose()
 * @method string getStatus()
 * @method Model\_Message[] get_Messages()
 * @method bool isExperiments()
 * @method bool isMessage()
 * @method bool isNoClose()
 * @method bool isStatus()
 * @method bool is_Messages()
 * @method $this setExperiments(Model\Experiment[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNoClose(bool $value)
 * @method $this setStatus(string $value)
 * @method $this set_Messages(Model\_Message[] $value)
 * @method $this unsetExperiments()
 * @method $this unsetMessage()
 * @method $this unsetNoClose()
 * @method $this unsetStatus()
 * @method $this unset_Messages()
 */
class SyncResponse extends Response
{
    const JSON_PROPERTY_MAP = [
        'experiments' => 'Model\Experiment[]',
        'no_close'    => 'bool',
    ];
}
