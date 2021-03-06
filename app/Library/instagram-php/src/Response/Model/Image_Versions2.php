<?php

namespace InstagramAPI\Response\Model;

use InstagramAPI\AutoPropertyMapper;

/**
 * Image_Versions2.
 *
 * @method AdditionalCandidate getAdditionalCandidates()
 * @method ImageCandidate[] getCandidates()
 * @method mixed getTraceToken()
 * @method bool isAdditionalCandidates()
 * @method bool isCandidates()
 * @method bool isTraceToken()
 * @method $this setAdditionalCandidates(AdditionalCandidate $value)
 * @method $this setCandidates(ImageCandidate[] $value)
 * @method $this setTraceToken(mixed $value)
 * @method $this unsetAdditionalCandidates()
 * @method $this unsetCandidates()
 * @method $this unsetTraceToken()
 */
class Image_Versions2 extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'candidates'            => 'ImageCandidate[]',
        'additional_candidates' => 'AdditionalCandidate',
        'trace_token'           => '',
    ];
}
