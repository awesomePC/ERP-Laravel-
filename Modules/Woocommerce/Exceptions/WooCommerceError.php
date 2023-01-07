<?php

namespace Modules\Woocommerce\Exceptions;

use Exception;

class WooCommerceError extends Exception
{
    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $output = ['success' => 0,
                    'msg' => $this->getMessage()
                ];

        if ($request->ajax()) {
            return $output;
        } else {
            throw new Exception($this->getMessage());
        }
    }
}
