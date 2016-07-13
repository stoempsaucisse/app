<?php

namespace Microffice\AccessControl\Support\Traits;

use Microffice\AccessControl\DecisionMaker;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

trait RegisterAclMasks
{
    // This traits registers masks from
    // optional $this->masks array and optional $mask argument array
    //
    // You need to add
    // $this->registerAclMasks($events);
    // in your boot method

    /**
     * Register object masks for Access Control.
     *
     * @param Illuminate\Contracts\Events\Dispatcher $events
     * @param array|null     $masks
     * @return void
     */
    protected function registerAclMasks(DispatcherContract $events, $masks = null)
    {
        $masks = (!is_null($masks)) ? (array) $masks : [];

        if (isset($this->masks)) {
            $masks = array_merge($masks, (array) $this->masks);
        }
        foreach ($masks as $mask => $code) {
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // Remove any mask with code == IDDQD
            // The MASK_IDDQD is an indication of the highest code available
            // It prevents permission check to work properly since
            // (bool) ($IDDQD & (any mask < $IDDQD)) === true
            // This would grant 'all' access for the given mask
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $IDDQD = constant(get_class(app(MaskBuilderContract::class)) . '::MASK_IDDQD');
            if ($code != $IDDQD) {
                $events->listen(DecisionMaker::$gatherMaskEventName, function () use ($mask, $code) {
                    return [$mask => $code];
                });
            }
        }
    }
}
